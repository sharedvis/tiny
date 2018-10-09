<?php

namespace Tiny\Helper;

class StaticData
{
    /**
     * Instance connection
     * @var object
     */
    protected $_instance;

    function __construct($type = 'redis', $config)
    {
        switch ($type) {
            case 'redis':
                $this->_instance = new \Redis();
                $this->_instance->connect(
                    $config->host,
                    $config->port,
                    $config->timeout
                );
                $this->_instance->select($config->database);
                break;

            default:
                # code...
                break;
        }
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->_instance, $name), $arguments);
    }

    public function save($key, $value)
    {
        $value = is_array($value) ? json_encode($value) : $value;

        return $this->_instance->set($key, $value);
    }
}
