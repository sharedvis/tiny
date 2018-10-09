<?php

namespace Tiny;

/**
* Sharedvis PHP SDK
*
* This is SDK for accessing Sharedvis API by autogenerate hmac
* as access key to service and send it as header.
*
* This class only for phalcon based application.
*/

use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;

use Tiny\Traits\FileUploadTraits;
use Tiny\Traits\BaseTraits;

class ApiRequest extends Plugin
{
    use BaseTraits, FileUploadTraits;

    /**
     * Construct new class
     * @param void
     */
    public function __construct($config)
    {
        // Assign configuration to the right place
        $this->_app_id = (isset($config['app_id'])) ? $config['app_id'] : null;
        $this->_app_secret = (isset($config['app_secret'])) ? $config['app_secret'] : null;
        $this->_host = (isset($config['host'])) ? $config['host'] : null;

        // Hmac Config
        $this->_hmac_config = (isset($config['hmac'])) ? $config['hmac'] : null;

        // Current session
        $this->_session = $this->dispatcher->getDI()->get('session');

        // When using CLI app the session cannot started
        // so we use cache as a fallback service, which is still using redis
        if (!$this->_session->isStarted() && $this->dispatcher->getDI()->has('cache'))
            $this->_session = $this->dispatcher->getDI()->get('cache');

        // Current token storage, it'll use session if token service is not exists
        if ($this->dispatcher->getDI()->has('token'))
            $this->_token_storage = $this->dispatcher->getDI()->get('token');
        else
            $this->_token_storage = $this->dispatcher->getDI()->get('session');
    }
}
