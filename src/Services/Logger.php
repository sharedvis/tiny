<?php
/**
 * This is plugin for logging
 *
 * @package  Tiny
 * @author   Rolies Deby <rolies@mbiz.co.id>
 */

namespace Tiny\Services;

use Raven_Autoloader;

class Logger
{
    /**
     * Current app configuration
     * @var object
     */
    var $config;

    /**
     * Current logger options
     * @var array
     */
    protected $options = [
        'logger' => 'sharedvis'
    ];

    /**
     * Current client instance
     * @var object
     */
    protected $_client;

    public function __construct($dsn, $options = [])
    {
        // Register raven autoloader
        Raven_Autoloader::register();

        if (empty($this->_client))
        {
            $options = array_merge($this->options, $options);

            $this->_client = new \Raven_Client($dsn, $options);

            $error_handler = new \Raven_ErrorHandler($this->_client);
            $error_handler->registerExceptionHandler();
            $error_handler->registerErrorHandler();
            $error_handler->registerShutdownFunction();
        }
    }

    /**
     * Process error logs
     * @param  mixed   $error  Error message
     * @return void
     */
    public function processLogs($error, $extra = [])
    {
        $sentry_id = null;

        if ($error instanceof \Exception ||
            $error instanceof Exception ||
            is_object($error))
        {
            $sentry_id = $this->_client->getIdent($this->_client->captureException($error, ['extra' => $extra]));
        } else {
            if (is_string($error))
                $sentry_id = $this->_client->getIdent($this->_client->captureMessage($error, [], $extra));
        }

        return $sentry_id;
    }

    /**
     * Set option for current logger
     * @param string $key       Option key
     * @param string $value     Option value
     */
    public function setOption($key, $value)
    {
        $this->options[$key] = $value;

        return $this;
    }
}
