<?php

namespace Tiny\Helper;

class SimpleCacheValue
{
    /**
     * Cache ttl
     * @var integer
     */
    public $ttl = 3600;

    /**
     * Current instance, supported instance :
     *
     * - Redis
     *
     * @var null
     */
    protected $instance = null;

    public function __construct($cache_instance)
    {
        $this->instance = $cache_instance;
    }

    /**
     * Call instance function
     * @param  string   $name       Function Name
     * @param  mixed    $arguments  Arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->instance, $name), $arguments);
    }

    /**
     * Get multikeys index
     * @param  string   $type               Cache prefix type
     * @param  array    $keys               Cache keys
     * @param  boolean  $refresh_expire     Refresh expire
     * @return void
     */
    public function getMultiKeysCache($type, $keys = [], $refresh_expire = false)
    {
        $cache_key = $this->getCacheKey($type, $keys);

        $value = $this->instance->get($cache_key);

        if (!empty($value) && $refresh_expire)
            $this->instance->expire($cache_key, $this->ttl);

        return $value;
    }

    /**
     * Set cache by multi keys
     * @param string    $type   Type of content as prefix
     * @param array     $keys   Collection of keys
     * @param mixed     $value  Value that will stored
     */
    public function setMultiKeysCache($type, $keys = [], $value = null, $no_expire = true)
    {
        $expire = [];
        $cache_key = $this->getCacheKey($type, $keys);

        if ($no_expire == false)
            $expire = ['nx', 'ex' => $this->ttl];

        // Cache the value
        $this->instance->set($cache_key, $value, $expire);

        // Assign this cache key as index members
        $this->setIndexMembers($type, $keys, $cache_key);
    }

    /**
     * Set index for member
     * @param string    $type      Type cache act as prefix
     * @param array     $keys      Collection of keys that will act as index
     * @param mixed     $cache_key Key of cache
     */
    public function setIndexMembers($type, $keys = [], $cache_key = null)
    {
        if (!empty($keys))
        {
            foreach ($keys as $key => $value)
            {
                $index_key = $this->getCacheKey($type, [$key => $value]);
                $this->instance->sAdd($index_key, $cache_key);
            }
        }
    }

    /**
     * Delete multikeys cache
     * @param string    $type   Type cache act as prefix
     * @param array     $keys   Collection of key value cache keys
     * @return void
     */
    public function deleteMultiKeysCache($type, $keys = [])
    {
        if (!empty($keys))
        {
            foreach ($keys as $key => $value)
            {
                $index_key = $this->getCacheKey($type, [$key => $value]);
                $cache_keys = $this->instance->sMembers($index_key);

                // Delete each cache
                if (!empty($cache_keys))
                {
                    foreach ($cache_keys as $cache_key)
                    {
                        $this->instance->delete($cache_key);
                    }
                }

                // Delete collections
                $this->instance->delete($index_key);
            }
        }
    }

    /**
     * Get cache key
     * @param  string $type         Key type
     * @param  array  $parameters   Parameters
     * @return string
     */
    public function getCacheKey($type, $parameters = [])
    {
        if (!is_array($parameters))
            throw new \Phalcon\Exception("Parameters should be an array.");

        $key = '';

        switch ($type) {
            case 'value':
                # Special case
                break;

            default:
                if (!empty($parameters)) {

                    $key = $key . $type;

                    foreach ($parameters as $k => $value)
                    {
                        $key = $key . '_' . $k . '_' . $value;
                    }

                } else {
                    $key = $type;
                }
                break;
        }

        return md5($key);
    }
}
