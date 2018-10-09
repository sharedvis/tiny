<?php
/**
 * ORMCache model trait
 *
 * @author Rolies Deby <rolies106@gmail.com>
 */

namespace Tiny\Traits;

/**
 * ORMCache Trait
 */
trait ORMCacheTrait
{
    /**
     * Remove this orm cache after save or update record
     * @var array
     */
    protected $_remove_orm_caches = [];

    /**
     * Current cache parameters
     * @var array
     */
    protected static $_cache_parameters = [];

    /**
     * ORM Cache before save hook
     * @return void
     */
    public function ormCacheBeforeSave()
    {
        if (self::isOrmCacheEnabled())
        {
            $primary_keys = (new \Phalcon\Mvc\Model\MetaData\Memory())->getPrimaryKeyAttributes($this);
            $pk_column = implode("_", $primary_keys);

            $parameters = [
                'models' => get_class($this),
                'bind' => [
                    'id' => (isset($this->{$pk_column})) ? $this->{$pk_column} : 0
                ]
            ];

            $this->deleteOrmCache(static::_createCachecKey($parameters, true));
        }
    }

    /**
     * ORM Cache after save hook
     * @return void
     */
    public function ormCacheAfterSave()
    {
        if (!empty($this->_remove_orm_caches))
        {
            foreach ($this->_remove_orm_caches as $key)
            {
                if(strpos($key, '*') !== false){
                    $key = str_replace('*', '', $key);
                    $keys = $this->getDI()->get('modelsCache')->queryKeys($key);
                    foreach ($keys as $key){
                        $this->getDI()->get('modelsCache')->delete($key);
                    }
                } else {
                    $this->getDI()->get('modelsCache')->delete($key);
                }
            }
        }
    }

    /**
     * Add orm cache keys to list delete caches
     * @param  mixed $keys  String or array of cache keys
     * @return Model
     */
    public function deleteOrmCache($keys)
    {
        if (is_string($keys)) {
            $this->_remove_orm_caches[] = $keys;
        } else if (is_array($keys)) {
            $this->_remove_orm_caches = array_merge($this->_remove_orm_caches, $keys);
        }

        $this->_remove_orm_caches = array_unique($this->_remove_orm_caches);


        return $this;
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return array
     */
    public static function ormCacheFindFirst($parameters = null)
    {
        // Convert the parameters to an array
        if (!is_array($parameters)) {
            $parameters = [$parameters];
        }

        $parameters = self::ormCacheParameters($parameters);

        return $parameters;
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return array
     */
    public static function ormCacheFind($parameters = null)
    {
        // Convert the parameters to an array
        if (!is_array($parameters)) {
            $parameters = [$parameters];
        }

        $parameters = self::ormCacheParameters($parameters);

        return $parameters;
    }

    /**
     * Allows to query to count result
     *
     * @param mixed $parameters
     * @return array
     */
    public static function ormCacheCount($parameters = null)
    {
        // Convert the parameters to an array
        if (!is_array($parameters)) {
            $parameters = [$parameters];
        }

        $parameters = self::ormCacheParameters($parameters);

        return $parameters;
    }

    /**
     * Append cache parameters
     * @param  array  $parameters Model parameters
     * @return array
     */
    public static function ormCacheParameters($parameters = [])
    {
        // Check if a cache key wasn't passed
        // and create the cache parameters
        if (self::isOrmCacheEnabled())
        {
            $cache_parameters = [
                "key"      => self::_createCachecKey(array_merge(['models' => get_called_class()], $parameters)),
                "lifetime" => self::ormCacheLifetime()
            ];

            if (!isset($parameters['cache'])) {
                if (empty(self::$_cache_parameters))
                    $parameters['cache'] = $cache_parameters;
                else
                    $parameters['cache'] = array_merge($cache_parameters, self::$_cache_parameters);
            } else {
                $parameters['cache'] = array_merge($cache_parameters, $parameters['cache']);
            }
        } else {
            // Remove any cache key if any
            if (isset($parameters['cache']))
                unset($parameters['cache']);
        }

        return $parameters;
    }

    /**
     * Get generated keys for ORM layer cache based on query
     * @param  array $parameters  Model parameters
     * @return string
     */
    protected static function _createCachecKey($parameters, $just_prefix = false)
    {
        // $uniqueKey = array();

        // if (isset($parameters['models']))
        // {
        //     $class = new \ReflectionClass($parameters['models']);
        //     $uniqueKey['model'] = $class->getShortName();

        //     unset($parameters['models']);
        // } else {
        //     $uniqueKey['model'] = $this->getShortClassName();
        // }

        // if (isset($parameters['bind']))
        // {
        //     foreach ($parameters['bind'] as $bind) {
        //         $uniqueKey[] = $bind;
        //     }

        //     unset($parameters['bind']);
        // }

        // return implode('_', $uniqueKey);

        if (isset($parameters['models']))
        {
            $class = new \ReflectionClass($parameters['models']);
            $class_short = $class->getShortName();

        } else {
            $class_short = self::getShortClassName();
        }

        $key = $class_short;

        if($just_prefix){
            $key .= "_*";
        } else {
            $key .= "_" . md5(json_encode($parameters));
        }

        return $key;
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

    /**
     * Check is orm cache enabled or not
     * @return boolean
     */
    public static function isOrmCacheEnabled()
    {
        return env('ORM_CACHE_ENABLE') == "true";
    }

    /**
     * Get lifetime
     * @return int
     */
    public static function ormCacheLifetime()
    {
        return env('ORM_CACHE_LIFETIME', 3600);
    }
}
