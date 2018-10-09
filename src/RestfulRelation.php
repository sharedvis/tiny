<?php

namespace Tiny;

/**
 * RestfulRelation class file
 */

/**
 * This is an base Model class for Restful Relation
 * @author Rolies Deby <rolies106@gmail.com>
 * @package tiny
 */

class RestfulRelation extends \Tiny\RestfulQuery
{
    /**
     * Relation name
     * @var string
     */
    public $name;

    /**
     * Sub-Relation collection
     * @var string
     */
    protected $_sub_relation = [];

    /**
     * Construct function
     * @param string $name          Relation name
     * @param array  $parameters    Relation parameters
     * @param array  $sub_relation  Collection of sub relation
     */
    public function __construct($name, $parameters = [], $sub_relation = [])
    {
        $this->name = $name;
        $this->_sub_relation = $sub_relation;
        $this->loadParameters($parameters);
    }

    /**
     * Quick initialization relation
     * @param  string $name         Relation name
     * @param  array  $parameters   Relation parameters
     * @return object
     */
    public static function init($name, $parameters = [], $sub_relation = [])
    {
        $instance = new self($name, $parameters, $sub_relation);

        return $instance;
    }

    /**
     * Get sub relation collection
     * @return array
     */
    public function getSubRelation()
    {
        return $this->_sub_relation;
    }

    /**
     * Set sub relation collection
     * @return array
     */
    public function setSubRelation($relations = [])
    {
        $this->_sub_relation = $relations;
    }

    /**
     * Convert current instance to array
     * @return array
     */
    public function toArray()
    {
        $parent_array = parent::toArray();
        $array = array_merge($parent_array, [
            'name' => $this->name,
            'sub_relation' => $this->_sub_relation
        ]);

        return $array;
    }

    /**
     * Load instance from array
     * @return array
     */
    public function fromArray($configs = [])
    {
        $object = parent::fromArray($configs);
        $object->name($configs['name']);
        $object->setSubRelation($configs['sub_relation']);

        return $object;
    }
}
