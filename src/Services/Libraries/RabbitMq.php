<?php
/**
 * RabbitMq
 *
 * @author Rolies Deby <rolies106@gmail.com>
 */

namespace Tiny\Services\Libraries;

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * RabbitMq
 */
class RabbitMq implements \Tiny\Services\Interfaces\Queue
{

    /**
     * Delay for worker to re-established connection to Rabbit MQ (in seconds)
     * @var
     */
    private $reconnect_delay;
    /**
     * Current connection information
     * @var array
     */
    protected $_connection_information;

    /**
     * Current connection
     * @var object
     */
    protected $_connection;

    /**
     * Data type
     * @var object
     */
    protected $_rabbit_type;

    /**
     * Queue class object
     * @var object
     */
    protected $_queue;

    /**
     * Number of prefetch count for each worker
     * @var int
     */
    protected $_prefetch_count;

    /**
     * Queue is bulk collection
     * @var object
     */
    protected $_is_bulk = false;

    /**
     * Construct class
     * @param array $connection_information
     * @param object $queue_object
     */
    public function __construct(array $connection_information, $queue_object)
    {
        $this->reconnect_delay = intval(env("RABBIT_MQ_RECONNECT_DELAY", "10"));

        $this->_connection_information = $connection_information;
        $this->_queue = $queue_object;

        $this->initConnection();

    }

    /**
     * Initialize Rabbit MQ connection
     */
    protected function initConnection(){

        $connection_information = $this->_connection_information;

        if (isset($connection_information['index']))
            $this->_elastic_index = $connection_information['index'];

        if (empty($this->_connection)) {
            $this->_connection = new AMQPConnection($connection_information['host'],
                $connection_information['port'],
                $connection_information['username'],
                $connection_information['password']);
            $this->_prefetch_count = isset($connection_information['prefetch_count']) ? $connection_information['prefetch_count'] : 1;
        }

    }

    /**
     * Send raw array data to rabbit to be processed
     * @param  string $operation Elasticsearch operation
     * @param  string $queue Queue name
     * @param  array $data Raw data
     * @return void
     */
    public function create($id, $data = [], $operation = 'insert', $queue = 'queue')
    {
        $this->put($queue, [
            'id' => $id,
            'model' => get_class($this->_queue->getModelCaller()),
            'operation' => $operation,
            'type' => $this->_rabbit_type,
            'is_bulk' => $this->_is_bulk,
            'data' => $data
        ]);
    }

    /**
     * Send raw array data to rabbit to be processed
     * @param  string $operation Elasticsearch operation
     * @param  string $queue Queue name
     * @param  array $data Raw data
     * @return void
     */
    public function createRaw($data = [], $operation = 'insert', $queue = 'queue')
    {
        $this->put($queue, [
            'model' => get_class($this->_queue->getModelCaller()),
            'operation' => $operation,
            'type' => $this->_rabbit_type,
            'is_bulk' => $this->_is_bulk,
            'data' => $data
        ]);
    }

    /**
     * Sends a job to the workers
     *
     * @param  string $queue_name Queue target name
     * @param  mixed $data Data that will be used by worker
     * @return void
     */
    public function put($queue_name, $data, $options = [])
    {
        $channel = $this->_connection->channel();
        $data = (is_array($data)) ? json_encode($data) : $data;
        $channel->queue_declare(
            $queue_name,        #queue - Queue names may be up to 255 bytes of UTF-8 characters
            false,              #passive - can use this to check whether an exchange exists without modifying the server state
            true,               #durable, make sure that RabbitMQ will never lose our queue if a crash occurs - the queue will survive a broker restart
            false,              #exclusive - used by only one connection and the queue will be deleted when that connection closes
            false               #auto delete - queue is deleted when last consumer unsubscribes
        );
        // Merge options
        $options = array_merge(
            $options,
            ['delivery_mode' => 2] # make message persistent, so it is not lost if server crashes or quits
        );
        $msg = new AMQPMessage(
            $data,
            $options
        );
        $channel->basic_publish(
            $msg,               #message
            '',                 #exchange
            $queue_name     #routing key (queue)
        );
        $channel->close();
        // $this->_connection->close();
    }

    /**
     * Set rabbit data type
     * @param string $type Data type
     */
    public function setType($type)
    {
        $this->_rabbit_type = $type;

        return $this;
    }

    /**
     * Set rabbit data bulk
     * @param boolean $bulk Is buld
     */
    public function setBulk($bulk)
    {
        $this->_is_bulk = $bulk;

        return $this;
    }

    /**
     * Listen queue job
     * @param  string $queue_name Queue name
     * @param  string $data Queue callback
     * @return void
     */
    public function listen($queue_name, $callback)
    {
        $channel = $this->initListenChannel($queue_name, $callback);

        while (count($channel->callbacks)) {
            try {
                echo "Waiting for incoming messages\n";
                $channel->wait();
            } catch (\Exception $e) {
                do {
                    echo "Re-creating connection... sleep for " . $this->reconnect_delay . " seconds\n";
                    sleep($this->reconnect_delay);

                    echo "Try to re-connect to Rabbit MQ server...\n";
                    try {
                        $this->_connection->reconnect();
                        $channel = $this->initListenChannel($queue_name, $callback);
                    } catch (\Exception $ex) {
                        echo "Failed to re-connect...\n";
                    }
                } while (!$this->_connection->isConnected());
            }

        }
        $channel->close();
        $connection->close();
    }

    /**
     * Initialize channel for listen action
     * @param $queue_name
     * @param $callback
     * @return channel
     */
    protected function initListenChannel($queue_name, $callback)
    {
        $channel = $this->_connection->channel();
        $channel->queue_declare(
            $queue_name,        #queue
            false,              #passive
            true,               #durable, make sure that RabbitMQ will never lose our queue if a crash occurs
            false,              #exclusive - queues may only be accessed by the current connection
            false               #auto delete - the queue is deleted when all consumers have finished using it
        );
        /**
         * don't dispatch a new message to a worker until it has processed and
         * acknowledged the previous one. Instead, it will dispatch it to the
         * next worker that is not still busy.
         */
        $channel->basic_qos(
            null,   #prefetch size - prefetch window size in octets, null meaning "no specific limit"
            $this->_prefetch_count,      #prefetch count - prefetch window in terms of whole messages
            null    #global - global=null to mean that the QoS settings should apply per-consumer, global=true to mean that the QoS settings should apply per-channel
        );
        /**
         * indicate interest in consuming messages from a particular queue. When they do
         * so, we say that they register a consumer or, simply put, subscribe to a queue.
         * Each consumer (subscription) has an identifier called a consumer tag
         */
        $channel->basic_consume(
            $queue_name,            #queue
            '',                     #consumer tag - Identifier for the consumer, valid within the current channel. just string
            false,                  #no local - TRUE: the server will not send messages to the connection that published them
            false,                  #no ack, false - acks turned on, true - off.  send a proper acknowledgment from the worker, once we're done with a task
            false,                  #exclusive - queues may only be accessed by the current connection
            false,                  #no wait - TRUE: the server will not respond to the method. The client should not wait for a reply method
            $callback               #callback
        );

        return $channel;
    }

    /**
     * Consume all job in queue and exit after all job is done
     * @param  string $queue_name Queue name
     * @param  string $data Queue callback
     * @return void
     */
    public function consume($queue_name, $callback)
    {
        $channel = $this->_connection->channel();
        $channel->queue_declare(
            $queue_name,        #queue
            false,              #passive
            true,               #durable, make sure that RabbitMQ will never lose our queue if a crash occurs
            false,              #exclusive - queues may only be accessed by the current connection
            false               #auto delete - the queue is deleted when all consumers have finished using it
        );
        /**
         * don't dispatch a new message to a worker until it has processed and
         * acknowledged the previous one. Instead, it will dispatch it to the
         * next worker that is not still busy.
         */
        $channel->basic_qos(
            null,   #prefetch size - prefetch window size in octets, null meaning "no specific limit"
            $this->_prefetch_count,      #prefetch count - prefetch window in terms of whole messages
            null    #global - global=null to mean that the QoS settings should apply per-consumer, global=true to mean that the QoS settings should apply per-channel
        );
        /**
         * indicate interest in consuming messages from a particular queue. When they do
         * so, we say that they register a consumer or, simply put, subscribe to a queue.
         * Each consumer (subscription) has an identifier called a consumer tag
         */
        $channel->basic_consume(
            $queue_name,            #queue
            '',                     #consumer tag - Identifier for the consumer, valid within the current channel. just string
            false,                  #no local - TRUE: the server will not send messages to the connection that published them
            false,                  #no ack, false - acks turned on, true - off.  send a proper acknowledgment from the worker, once we're done with a task
            false,                  #exclusive - queues may only be accessed by the current connection
            false,                  #no wait - TRUE: the server will not respond to the method. The client should not wait for a reply method
            $callback               #callback
        );

        try {
            while (count($channel->callbacks)) {
                //n-sec timeout
                $channel->wait(null, false, 10);
            }
        } catch (\PhpAmqpLib\Exception\AMQPTimeoutException $e) {
            $channel->queue_delete($queue_name, $if_unused = false, $if_empty = true, $nowait = false, $ticket = null);
        }

        // $connection->close();
    }

    /**
     * Set global job queue for error handling in cli.php
     * @param  AMQPMessage $msg Queue data
     * @return void
     */
    public function setHandlerErrorJobQueue(AMQPMessage $msg)
    {
        //global variabel for take out error job from queue
        $GLOBALS['errorQueue'] = $msg;
        $GLOBALS['errorDataQueue'] = $msg->body;
    }

    /**
     * Process delete from queue job
     * @param  string $message Log running process
     * @return void
     */
    public function logProcess($message)
    {
        echo date('d-m-Y H:i:s') . ": Processing " . $message . "\n";
    }
}
