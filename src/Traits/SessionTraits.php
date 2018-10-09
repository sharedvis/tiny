<?php
/**
 * API base commands
 *
 * @package Tiny
 * @author Rolies Deby <rolies106@gmail.com>
 */

namespace Tiny\Traits;

trait SessionTraits
{
    /**
     * Current application session
     * @var object
     */
    protected $_session;

    /**
     * Get current value inside session
     * @param  string $name Session key name
     * @return string
     */
    public function getSession($name)
    {
        $name = $this->generateSessionKey($name);

        if (is_object($this->_session)) {
            if (method_exists($this->_session, 'get')) {
                return $this->_session->get($name);
            }
        } else {
            if (isset($this->_session[$name]))
                return $this->_session[$name];
        }

        return null;
    }

    /**
     * Set session
     * @param string $name  Session key name
     * @param string $value Session value
     */
    public function setSession($name, $value = null)
    {
        $name = $this->generateSessionKey($name);

        if (is_object($this->_session)) {
            if (method_exists($this->_session, 'set')) {
                return $this->_session->set($name, $value);
            }
        } else {
            if (isset($this->_session[$name]))
                $this->_session[$name] = $value;
        }
    }

    /**
     * Generate key for current session by string
     * @param  string   $name   Session key name
     * @return string
     */
    protected function generateSessionKey($name)
    {
        if (method_exists($this->_session, 'getId'))
            return $name . "_" . $this->_session->getId();

        return $name;
    }
}
