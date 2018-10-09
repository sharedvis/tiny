<?php

namespace Tiny\Services\Interfaces;
use \PhpAmqpLib\Message\AMQPMessage;
interface Task 
{
    /**
     * This method will fire for every task
     *
     * @return void
     */
    public function fire(AMQPMessage $msg);
}