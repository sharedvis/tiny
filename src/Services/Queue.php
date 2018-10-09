<?php
/**
 * Queue Service
 *
 * @author Rolies Deby <rolies106@gmail.com>
 */

namespace Tiny\Services;

use \Tiny\Services\RabbitMq\RabbitMq;

class Queue
{
    /**
     * Queue engine type
     * @var string
     */
    protected $_engine = null;

    /**
     * Connection credential information
     * @var array
     */
    protected $_connection_credential = [];

    /**
     * Connection to queue engine
     * @var object
     */
    protected $_connection = null;

    /**
     * Caller model object
     * @var object
     */
    protected $_model_caller;

    /**
     * Contruct class
     * @param string $engine                    Engine type
     * @param array  $connection_information    Information that will use for connect
     */
    public function __construct($engine, $connection_information = [])
    {
        // Assign connection information
        $this->_engine = $engine;
        $this->_connection_credential = $connection_information;

        switch ($engine) {
            case 'rabbitmq':
                $this->_connection = new \Tiny\Services\Libraries\RabbitMq($this->_connection_credential, $this);
                break;

            default:
                throw new \Exception("Queue is not supported for \"$engine\" yet.");
                break;
        }
    }

    /**
     * Call function in this class or search library object
     * @param  string   $name      Method name
     * @param  array    $arguments Method arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this, $name))
            return call_user_func_array([$this, $name], $arguments);
        elseif (!empty($this->_connection))
            return call_user_func_array([$this->_connection, $name], $arguments);
        else
            throw new \Exception("Method {$name} is not found.");
    }

    /**
     * Get connection to queue engine
     * @return mixed
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * Set current queue engine service
     * @return object
     */
    public function setQueueEngine($object)
    {
        $this->_queue_engine = $object;

        return $this;
    }

    /**
     * Set model object caller
     * @param object    $model  Model object
     * @return object
     */
    public function setModelCaller($model)
    {
        $this->_model_caller = $model;

        return $this;
    }

    /**
     * Get model object caller
     * @param object    $model  Model object
     * @return object
     */
    public function getModelCaller()
    {
        return $this->_model_caller;
    }
}
