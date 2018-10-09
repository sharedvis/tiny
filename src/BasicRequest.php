<?php

namespace Tiny;

/**
* Sharedvis PHP SDK
*
* This is SDK for accessing Sharedvis API by autogenerate hmac
* as access key to api and send it as header.
*/

use Tiny\Traits\FileUploadTraits;
use Tiny\Traits\BaseTraits;

class BasicRequest
{
    use BaseTraits, FileUploadTraits;

    /**
     * Construct new class
     * @param void
     */
    public function __construct($config)
    {
        if (session_status() == PHP_SESSION_NONE)
            @session_start();

        // Assign configuration to the right place
        $this->_app_id = (isset($config['app_id'])) ? $config['app_id'] : null;
        $this->_app_secret = (isset($config['app_secret'])) ? $config['app_secret'] : null;
        $this->_host = (isset($config['host'])) ? $config['host'] : null;

        // Hmac Config
        $this->_hmac_config = (isset($config['hmac'])) ? $config['hmac'] : null;

        // Current session
        $this->_session = $_SESSION;
    }

    /**
     * Override Host which already set on init
     *
     * @param string $host
     */
    public function setHost($host){
        $this->_host = $host;
    }

    /**
     * Get current Host
     *
     * @return string
     */
    public function getHost(){
        return $this->_host;
    }
}
