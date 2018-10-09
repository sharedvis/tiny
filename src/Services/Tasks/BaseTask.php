<?php

namespace Tiny\Services\Tasks;

use Danzabar\CLI\Tasks\Task;
use Danzabar\CLI\Input\InputOption;

use Tiny\Services\Models\EmailModel;
use Tiny\Helper\GlobalFunction;

class BaseTask extends Task  
{

    /**
     * @var ApiRequest
     */
    private $tiny = null;
    

    /**
    * Every task should have a main method, it will be the default
    * action that is called if no other is specified.
    *
    * We use the Annotation engine to specify which commands are actions. Make sure you include it on every action!
    *
    * @Action
    */
    public function main()
    {
	$reflectionClass = new \ReflectionClass($this);
        $pos = strpos($reflectionClass->name, "Tasks");
        $str_class = substr_replace($reflectionClass->name, "", 0, $pos + strlen("Tasks"));
        $queue_name = GlobalFunction::getConvensionName(str_replace("\\", "", $str_class)); 
        $this->getDI()->getQueue()->listen($queue_name, [$this, 'fire']);
    }

    /**
     * Get queue engine connection
     * @return object
     */
    public function getQueueEngine()
    {
        return $this->getDI()->getQueue()->getConnection();
    }

    /**
     * Setup current task
     * @return void
     */
    public function setupMain()
    {
        // $this->option->addExpected('queue', InputOption::REQUIRED);
    }

    protected function initDbConnection()
    {
        try {
            $this->getQueueEngine()->logProcess('Init DB Connection');
            $this->getDI()->getShared('db_read')->connect();
            $this->getDI()->getShared('db')->connect();
        } catch (\Exception $e) {
            $this->getQueueEngine()->logProcess('Init DB Error...');
            throw $e;
        }
    }

    protected function closeDBConnection()
    {
        try {
            $this->getQueueEngine()->logProcess('Closing DB Connection');
            $this->getDI()->getShared('db_read')->close();
            $this->getDI()->getShared('db')->close();
        } catch (\Exception $e) {
            $this->getQueueEngine()->logProcess('Close DB error...');
            throw $e;
        }
    }

    
}
