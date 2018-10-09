<?php
/**
 * Search engine
 *
 * @author Rolies Deby <rolies106@gmail.com>
 */

namespace Tiny\Services;

use \Tiny\Services\ElasticSearch\ElasticSearch;

class Search
{
    /**
     * Search engine type
     * @var string
     */
    protected $_engine = null;

    /**
     * Connection credential information
     * @var array
     */
    protected $_connection_credential = [];

    /**
     * Connection to search engine
     * @var object
     */
    protected $_connection = null;

    /**
     * Contruct class
     * @param string $engine                    Engine type
     * @param array  $connection_information    Information that will use for connect
     */
    public function __construct($engine, $connection_information = [])
    {
        // Assign connection information
        $this->_engine = $engine;
        $this->_connection_credential = $connection_information;

        switch ($engine) {
            case 'elasticsearch':
                $this->_connection = new \Tiny\Services\Libraries\ElasticSearch($this->_connection_credential, $this);
                break;

            default:
                throw new \Exception("Search is not supported for \"$engine\" yet.");
                break;
        }
    }

    /**
     * Call function in this class or search library object
     * @param  string   $name      Method name
     * @param  array    $arguments Method arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this, $name))
            return call_user_func_array([$this, $name], $arguments);
        elseif (!empty($this->_connection))
            return call_user_func_array([$this->_connection, $name], $arguments);
        else
            throw new \Exception("Method {$name} is not found.");
    }

    /**
     * Get connection to search engine
     * @return mixed
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * Set current search engine service
     * @return object
     */
    // public function setSearchEngine($object)
    // {
    //     $this->_search_engine = $object;

    //     return $this;
    // }

    /**
     * Clean data by whitelist
     * @param  array    $data       Collection of data
     * @param  array    $whitelist  Collection of whitelist column
     * @return array
     */
    public function cleanDataByWhitelist($data, $whitelist)
    {
        foreach ($data as $key => $value)
        {
            if (!in_array($key, $whitelist))
                unset($data[$key]);
        }

        return $data;
    }

    /**
     * Clean data type based on mapping
     * @param  array  $data    Array collection of data
     * @param  array  $mapping Mapping data type for search engine
     * @return array
     */
    public function cleanDataTypeByMapping($data = [], $mapping = [])
    {
        foreach ($data as $key => $value)
        {
            if (isset($mapping[$key]))
            {
                if (isset($mapping[$key]['type']))
                {
                    switch ($mapping[$key]['type']) {
                        case 'long':
                        case 'integer':
                        case 'short':
                            $data[$key] = (integer) $data[$key];
                            break;

                        case 'double':
                        case 'float':
                            $data[$key] = (double) $data[$key];
                            break;

                        default:
                            continue;
                            break;
                    }
                }
            }
        }

        return $data;
    }
}
