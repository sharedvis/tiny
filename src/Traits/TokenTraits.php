<?php
/**
 * API base commands for token management
 *
 * @package Tiny
 * @author Rolies Deby <rolies106@gmail.com>
 */

namespace Tiny\Traits;

trait TokenTraits
{
    /**
     * Current application token storage
     * @var object
     */
    protected $_token_storage;

    /**
     * Get current value inside token storage
     * @param  string $access_token Access token
     * @return string
     */
    public function getTokenDetail($access_token)
    {
        if (is_object($this->_token_storage)) {
            if (method_exists($this->_token_storage, 'get')) {
                return $this->_token_storage->get($access_token);
            }
        } else {
            if (isset($this->_token_storage[$access_token]))
                return $this->_token_storage[$access_token];
        }

        return null;
    }

    /**
     * Set session
     * @param string $access_token  Access token
     * @param string $value         Value
     */
    public function setTokenDetail($access_token, $value = null)
    {
        if (is_object($this->_token_storage)) {
            if (method_exists($this->_token_storage, 'get')) {
                return $this->_token_storage->set($access_token, $value);
            }
        } else {
            if (isset($this->_token_storage[$access_token]))
                $this->_token_storage[$access_token] = $value;
        }
    }
}
