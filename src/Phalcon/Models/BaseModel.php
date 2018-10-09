<?php

namespace Tiny\Phalcon\Models;

use Tiny\Helper\Time;
use Tiny\Traits\ORMCacheTrait;
use Tiny\Traits\ModelPaginationTrait;
use Tiny\Services\Traits\SearchEngineTraits;
use Tiny\Traits\ConstTraits;

class BaseModel extends \Phalcon\Mvc\Model
{
    use ORMCacheTrait, ModelPaginationTrait, SearchEngineTraits, ConstTraits;

    /**
     * Is called from relation
     * @var array
     */
    protected $_is_called_from_relation = false;

    /**
     * Hide this field
     * @var array
     */
    protected $_hidden = [];

    /**
     * Whitelist when do save or create using mass assignment
     * @var array
     */
    protected $_whitelist = [];

    /**
     * Extra field that will append when using toArray function
     * @var array
     */
    protected $_extra_fields = [];

    /**
     * Always include this relation
     * @var array
     */
    protected $_with_relation = [];

    /**
     * Store previous object before it's updated
     * @var ModerationList
     */
    protected $_prev_record;

    /**
     * Store toArray() result
     * @var array
     */
    protected $_to_array_result;

    /**
     * Extra information of object
     * @var array
     */
    protected $_custom_extra_information = [];

    /**
     * Custom events
     * @var array
     */
    protected $_custom_events = [];

    /**
     * Initializing model
     * @return void
     */
    public function initialize()
    {
        $this->setReadConnectionService('db_read');
        $this->setWriteConnectionService('db');
    }

    /**
     * Close DB connection
     * @param  string   $service_name   Service
     * @return void
     */
    public function closeDbConnection($service_name = null)
    {
        if (!empty($service_name))
            return $this->getDI()->getShared($service_name)->close();

        $this->getDI()->getShared('db_read')->close();
        $this->getDI()->getShared('db')->close();

        return true;
    }

    /**
     * Reconnect to DB
     * @param  string   $service_name   Service
     * @return void
     */
    public function connectToDb($service_name = null)
    {
        if (!empty($service_name))
            return $this->getDI()->getShared($service_name)->connect();

        $this->getDI()->getShared('db_read')->connect();
        $this->getDI()->getShared('db')->connect();

        return true;
    }

    /**
     * Return short class name without namespace
     * @return string
     */
    public function getShortClassName()
    {
        $class = new \ReflectionClass(get_class($this));
        return $class->getShortName();
    }

    /**
     * Before save hook
     * @return void
     */
    public function beforeSave()
    {
        $this->sanitizeColumn();

        // Trigger orm trait before save hook
        $this->ormCacheBeforeSave();
    }

    /**
     * After save hook
     * @return void
     */
    public function afterSave()
    {
        // Trigger orm trait after save hook
        $this->ormCacheAfterSave();
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BaseModel
     */
    public static function findFirst($parameters = null)
    {
        $parameters = static::ormCacheFindFirst($parameters);

        return parent::findFirst($parameters);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BaseModel
     */
    public static function find($parameters = null)
    {
        $parameters = static::ormCacheFind($parameters);

        return parent::find($parameters);
    }

    /**
     * Allows to query count result
     *
     * @param mixed $parameters
     * @return BaseModel
     */
    public static function count($parameters = null)
    {
        $parameters = static::ormCacheCount($parameters);

        return parent::count($parameters);
    }

    /**
     * Select database read connection
     *
     * @param array $intermediate
     * @param array $bindParams
     * @param array $bindTypes
     */
    // public function selectReadConnection($intermediate, $bindParams, $bindTypes) { }

    /**
     * Select database write connection
     *
     * @param array $intermediate
     * @param array $bindParams
     * @param array $bindTypes
     */
    // public function selectWriteConnection($intermediate, $bindParams, $bindTypes) { }

    /**
     * Before update event
     * @return void
     */
    public function beforeUpdate()
    {
        $this->sanitizeColumn();

        $this->_prev_record = clone $this;
    }

    /**
     * After update event
     * @return void
     */
    public function afterUpdate()
    {
        $this->sanitizeColumn();
    }

    /**
     * Before create event
     * @return void
     */
    public function beforeCreate()
    {
        $this->sanitizeColumn();
    }

    /**
     * After create event
     * @return void
     */
    public function afterCreate()
    {
        $this->sanitizeColumn();
    }

    /**
     * Before before delete event
     * @return void
     */
    public function beforeDelete()
    {
        
    }

    /**
     * After fetch event
     * @return void
     */
    public function afterFetch()
    {
        $this->sanitizeColumn();
    }

    /**
     * Change date format from UTC to default datetime.
     * @param  array  $columns key value of table record
     * @return mixed
     */
    public function changeDateFromUtc($columns = [])
    {
        if (empty($columns))
        {
            if (property_exists($this, 'updated_at'))
                $this->updated_at = Time::toLocal($this->updated_at);

            if (property_exists($this, 'created_at'))
                $this->created_at = Time::toLocal($this->created_at);

        } else if (!empty($columns)) {

            if (isset($columns['updated_at']))
                $columns['updated_at'] = Time::toLocal($columns['updated_at']);

            if (isset($columns['created_at']))
                $columns['created_at'] = Time::toLocal($columns['created_at']);

            return $columns;
        }
    }

    /**
     * Sanitize column on DB events
     * @param  array  $columns Key value of table record
     * @return mixed
     */
    public function sanitizeColumn($columns = [])
    {
        if (empty($columns))
        {
            if (property_exists($this, 'id'))
                $this->id = (int) $this->id;

            if (property_exists($this, 'updated_at'))
                $this->updated_at = \Tiny\Helper\Time::toUtc($this->updated_at);

            if (property_exists($this, 'created_at'))
                $this->created_at = \Tiny\Helper\Time::toUtc($this->created_at);

            if (property_exists($this, 'status'))
                $this->status = (int) $this->status;

        } else if (!empty($columns)) {

            if (isset($columns['id']))
                $columns['id'] = (int) $columns['id'];

            if (isset($columns['updated_at']))
                $columns['updated_at'] = \Tiny\Helper\Time::toUtc($columns['updated_at']);

            if (isset($columns['created_at']))
                $columns['created_at'] = \Tiny\Helper\Time::toUtc($columns['created_at']);

            if (isset($columns['status']))
                $columns['status'] = (int) $columns['status'];

            return $columns;
        }
    }

    /**
     * Append hidden field
     * @param object
     */
    public function addHidden($value)
    {
        if (is_array($value))
            $this->_hidden = array_merge($this->_hidden, $value);
        else
            $this->_hidden = array_merge($this->_hidden, [$value]);

        return $this;
    }

    /**
     * Get toArray() result
     * @return array
     */
    public function getToArray()
    {
        return $this->_to_array_result;
    }

    /**
     * Override model to array conversion function
     * @param  array    $columns        List of columns that want to display
     * @param  boolean  $return_object  Return current object for chaining process
     * @return array
     */
    public function toArray($columns = array(), $return_object = false)
    {
        if (!empty($columns))
            $return = parent::toArray($columns);
        else
            $return = parent::toArray();

        // Sanitize values
        $return = $this->sanitizeColumn($return);

        foreach ($this->_hidden as $hidden) {
            unset($return[$hidden]);
        }

        if ((isset($this->_elastic_data_exclude) && is_array($this->_elastic_data_exclude)))
        {
            foreach ($this->_elastic_data_exclude as $exclude)
            {
                unset($return[$exclude]);
            }
        }

        // Hide hidden value
        // $return = array_diff($return, $this->_hidden);

        if (is_array($this->_with_relation) && !empty($this->_with_relation))
        {
            foreach ($this->_with_relation as $key => $relation)
            {
                if (is_array($relation)) {
                    $relation_collection = $this->_getRelation($this, $key);
                    // $relation_collection = array_merge($relation_collection, [$key => $this->_getRelationArray($this->{$key}, $relation)]);
                    $return[$key] = $relation_collection;
                } else {
                    $relation_collection = $this->_getRelation($this, $relation);
                    $return[$relation] = $relation_collection;
                }
            }
        }

        if (!empty($this->_extra_fields))
        {
            foreach ($this->_extra_fields as $attributes) {
                if (property_exists($this, $attributes))
                    $return[$attributes] = $this->{$attributes};
            }
        }

        if ($return_object)
        {
            $this->_to_array_result = $return;

            return $this;
        }

        return $return;
    }

    /**
     * Override save function to get whitelist from model var
     * @param  array    $data   Key value data that will be saved to table
     * @return mixed
     */
    public function save($data = [], $whitelist = [])
    {
        $whitelist = (!empty($whitelist)) ? array_merge($this->_whitelist, $whitelist) : $this->_whitelist;

        return parent::save($data, $whitelist);
    }

    /**
     * Override create function to get whitelist from model var
     * @param  array    $data   Key value data that will be saved to table
     * @return mixed
     */
    public function create($data = [], $whitelist = [])
    {
        $whitelist = (!empty($whitelist)) ? array_merge($this->_whitelist, $whitelist) : $this->_whitelist;

        return parent::create($data, $whitelist);
    }

    /**
     * Include table relation before return as an array with toArray function
     * @param mixed $relation Table relation
     */
    public function with($relation)
    {
        if (empty($relation))
            return $this;

        if (is_array($relation))
            $this->_with_relation = array_merge($this->_with_relation, $relation);
        else
            $this->_with_relation[] = $relation;

        return $this;
    }

    /**
     * Return current model error message in array type
     * @return array
     */
    public function getMessagesArray()
    {
        $messages = $this->getMessages();
        $messages_arr = [];

        if (!empty($messages))
        {
            foreach ($messages as $message) {
                $messages_arr[$message->getField()] = $message->getMessage();
            }
        }

        return $messages_arr;
    }

    /**
     * Get multilevel relation
     * @param object $model Model instance
     * @param array  $relation Relation list
     * @return [type]           [description]
     */
    protected function _getRelationArray($model, $relations = [])
    {
        $return = [];

        if (is_array($relations))
        {
            foreach ($relations as $key => $relation)
            {
                if (is_array($relation)) {
                    $relation_collection = $this->_getRelation($model, $key);
                    $relation_collection = array_merge($relation_collection, $this->_getRelationArray($model->{$key}, $relation));
                    $return[$key] = $relation_collection;
                } else {
                    $relation_collection = $this->_getRelation($model, $relation);
                    $return[$relation] = $relation_collection;
                }
            }
        }

        return $return;
    }

    /**
     * Get relation content
     * @param object $model Model instance
     * @param string $relation Model relation name
     * @return string
     */
    protected function _getRelation($model, $relation)
    {
        $relation_collection = [];

        if (!empty($model->{$relation}))
        {
            if ($model->{$relation} instanceof \Phalcon\Mvc\Model\Resultset\Simple)
            {
                foreach ($model->{$relation} as $key => $rel)
                {
                    $rel->_is_called_from_relation = true;

                    // if ($this->_generate_data_for_elastic)
                    //     $rel = $rel->setGenerateDataForElasticFlag(true);

                    $include_subrelation = (\Tiny\Helper\ArrayProcessor::findByKey($this->_with_relation, $relation)) ?: [];
                    $relation_collection[$key] = $rel->with($include_subrelation)->toArray();

                    if (!empty($rel->_extra_fields))
                    {
                        foreach ($rel->_extra_fields as $attributes) {
                            if (property_exists($rel, $attributes))
                                $relation_collection[$key][$attributes] = $rel->{$attributes};
                        }
                    }
                }
            } else {
                $model->{$relation}->_is_called_from_relation = true;

                // if ($this->_generate_data_for_elastic)
                //     $model->{$relation} = $model->{$relation}->setGenerateDataForElasticFlag(true);

                $include_subrelation = (\Tiny\Helper\ArrayProcessor::findByKey($this->_with_relation, $relation)) ?: [];
                $relation_collection = $model->{$relation}->with($include_subrelation)->toArray();

                if (!empty($model->{$relation}->_extra_fields))
                {
                    foreach ($model->{$relation}->_extra_fields as $attributes)
                    {
                        if (property_exists($model->{$relation}, $attributes))
                            $relation_collection[$attributes] = $model->{$relation}->{$attributes};
                        else if (method_exists($model->{$relation}, $attributes))
                            $relation_collection[$attributes] = $model->{$relation}->{$attributes}();
                    }
                }
            }
        }

        return $relation_collection;
    }

    /**
     * Get current database transaction
     * @return object
     */
    public function getCurrentTransaction()
    {
        $txManager = $this->getDI()->get('transactions');
        $transaction = $txManager->get();

        return $transaction;
    }

    /**
     * Get old record
     * @return object
     */
    public function getPrevRecord()
    {
        return $this->_prev_record;
    }

    /**
     * Add extra object information by key value
     * @param array     $array  Array of extra information
     */
    public function addExtraObjectInformation($array)
    {
        $this->_custom_extra_information = array_merge($this->_custom_extra_information, $array);

        return $this;
    }

    /**
     * Get extra object information by key
     * @param string     $key  Array key
     */
    public function getExtraObjectInformation($key)
    {
        if (isset($this->_custom_extra_information[$key]))
            return $this->_custom_extra_information[$key];

        return false;
    }

    /**
     * Add custom event trigger
     * @param string    $event          Event name
     */
    public function addCustomEvent($event)
    {
        array_push($this->_custom_events, $event);

        return $this;
    }

    /**
     * Add custom event trigger
     * @param string    $event          Event name
     * @param boolean   $remove_event   Remove event if exists
     */
    public function isCustomEventExists($event, $remove_event = false)
    {
        if (!$remove_event)
            return (array_search($event, $this->_custom_events) !== false);

        $key = array_search($event, $this->_custom_events);

        if ($key !== false)
        {
            unset($this->_custom_events[$key]);
            return true;
        }

        return false;
    }

    /**
     * Check status
     * @param  string $status Company status
     * @return boolean
     */
    public function checkStatus($status = 'active')
    {
        switch ($status) {
            case 'active':
                return $this->status == self::$_STATUS_ACTIVE;
                break;

            case 'delete':
            case 'deleted':
                return $this->status == self::$_STATUS_DELETED;
                break;

            case 'inactive':
                return $this->status == self::$_STATUS_IN_ACTIVE;
                break;

            case 'block':
            case 'blocked':
                return $this->status == self::$_STATUS_BLOCKED;
                break;

            default:
                return false;
                break;
        }
    }

    /**
     * Get status
     * @param  string $status Status string
     * @return boolean
     */
    public function getStatus($status = 'active')
    {
        if(!intval($status)){
            switch ($status) {
                case 'active':
                    return self::$_STATUS_ACTIVE;
                    break;

                case 'delete':
                case 'deleted':
                    return self::$_STATUS_DELETED;
                    break;

                case 'inactive':
                    return self::$_STATUS_INACTIVE;
                    break;

                case 'block':
                case 'blocked':
                    return self::$_STATUS_INACTIVE;
                    break;

                default:
                    return false;
                    break;
            }
        }
        else{
            return $status;
        }
    }

    /**
     * Generate slug
     *
     * @return string
     */
    public function generateSlug($column_source, $column_dest = 'slug')
    {
        $slug = \Phalcon\Utils\Slug::generate($this->{$column_source});
        $exists = $this->find([
            "conditions" => "{$column_dest} LIKE :code:",
            "bind" => ["code" => $slug . '%']
        ]);

        if ($exists->count() > 0) {
            $slug = $slug . '-' . $exists->count();
        }

        return $slug;
    }
}
