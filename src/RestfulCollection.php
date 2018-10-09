<?php

namespace Tiny;

/**
 * RestfulIterator class file
 */

/**
 * This is an base Model for object iteration
 * @author Rolies Deby <rolies106@gmail.com>
 * @package Tiny
 */

class RestfulCollection implements \Iterator
{
    /**
     * Current data result
     * @var array
     */
    protected $_data = [];

    /**
     * Current pagination info
     * @var array
     */
    protected $_pagination = [];

    /**
     * Current aggregation info
     * @var array
     */
    protected $_aggregations = [];

    /**
     * Current model called
     * @var object
     */
    protected $_model;

    public function __construct($data, $model)
    {
        if (isset($data->items) && isset($data->pagination))
        {
            $this->_data = $data->items;
            $this->_pagination = $data->pagination;
        } else {
            $this->_data = $data;
        }

        if (isset($data->aggregations))
            $this->_aggregations = $data->aggregations;

        $this->_model = $model;
    }

    public function rewind()
    {
        reset($this->_data);
    }

    public function current()
    {
        $data = current($this->_data);

        if (!empty($this->_model))
        {
            $model_class = get_class($this->_model);
            $new_object = new $model_class;

            foreach ($this->_model as $key => $value) {
                $new_object->{$key} = $value;
            }

            // Assign some needed data
            $new_object::$_return_as_collection = $this->_model::$_return_as_collection;

            foreach ($data as $key => $value)
            {
                $new_object->{$key} = $value;
                $this->{$key} = $value;
            }

            // After fetch hook
            $new_object->afterFetch();

            return $new_object;
        }

        return $data;
    }

    public function key()
    {
        $data = key($this->_data);
        return $data;
    }

    public function next()
    {
        $data = next($this->_data);
        return $data;
    }

    public function valid()
    {
        $key = key($this->_data);
        $data = ($key !== null && $key !== false);
        return $data;
    }

    public function __call($method = null, $parameters = null)
    {
        if (!empty($this->_model))
        {
            if (method_exists($this->_model, $method))
            {
                return call_user_func_array([$this->_model, $method], $parameters);
            }
        }
    }

    /**
     * Get current pagination info
     * @return object
     */
    public function getPagination()
    {
        if (get_called_class() != 'Tiny\RestfulCollection')
            throw new \Exception(__FUNCTION__ . " is for RestfulModel use only.");

        return $this->_pagination;
    }

    /**
     * Get current aggregations info
     * @return object
     */
    public function getAggregations()
    {
        if (get_called_class() != 'Tiny\RestfulCollection')
            throw new \Exception(__FUNCTION__ . " is for RestfulModel use only.");

        return $this->_aggregations;
    }

    /**
     * Get current data as raw result
     * @return object
     */
    public function getData()
    {
        throw new \Exception(__FUNCTION__ . " is for limited use only.");

        // if (get_called_class() != 'Tiny\RestfulCollection')
        //     throw new \Exception(__FUNCTION__ . " is for RestfulModel use only.");

        // return $this->_data;
    }

    /**
     * Get model object
     * @return mixed
     */
    public function getModel()
    {
        return $this->_model;
    }
}
