<?php

namespace Tiny;

/**
 * RestfulQuery class file
 */

/**
 * This is an base Model class for Restful Query processing,
 * only work for phalcon query for now.
 *
 * @author Rolies Deby <rolies106@gmail.com>
 * @package tiny
 */

class RestfulQuery
{
    /**
     * Query parameters
     * @var array
     */
    protected $_parameters = [];

    /**
     * Load parameters from array
     * @param  array  $parameters Parameters array
     * @return object
     */
    public function loadParameters($parameters = [])
    {
        if (!empty($parameters))
        {
            if (isset($parameters['conditions']) && isset($parameters['bind']))
                $this->addCondition($parameters['conditions'], $parameters['bind']);
            else if (isset($parameters['conditions']) && !isset($parameters['bind']))
                $this->addCondition($parameters['conditions']);
        }

        return $this;
    }

    /**
     * Append conditions to current parameters
     * @param string $conditions    SQL query conditions
     * @param array  $bind          Parameter binding
     * @param array  $operator      Operator
     * @return mixed
     */
    public function addCondition($conditions, $bind = [], $operator = 'AND')
    {
        if (isset($this->_parameters['conditions']) && !empty($conditions))
            $this->_parameters['conditions'] = $this->_parameters['conditions'] . ' ' . $operator . ' ' . $conditions;
        else
            $this->_parameters['conditions'] = $conditions;

        if (isset($this->_parameters['bind']) && !empty($bind))
            $this->_parameters['bind'] = array_merge($this->_parameters['bind'], $bind);
        else if (!empty($bind))
            $this->_parameters['bind'] = $bind;

        return $this;
    }

    /**
     * Set current parameters
     * @param array $parameters [description]
     * @return object
     */
    public function setParameters($parameters = [])
    {
        $this->_parameters = $parameters;

        return $this;
    }

    /**
     * Get current parameters
     * @return array
     */
    public function getParameters()
    {
        return $this->_parameters;
    }

    /**
     * Convert current instance to array
     * @return array
     */
    public function toArray()
    {
        $array = [
            'class' => get_class($this),
            'parameters' => $this->getParameters()
        ];

        return $array;
    }

    /**
     * Load instance from array
     * @return array
     */
    public function fromArray($configs = [])
    {
        $this->setParameters($configs['parameters']);

        return $this;
    }
}
