<?php

namespace Tiny;

/**
 * RestfulModel class file
 */

/**
 * This is an base Model class for Restful Web Service
 * @author Rolies Deby <rolies106@gmail.com>
 * @package tiny
 */

use Tiny\RestfulTransactionsManager as TransactionManager;
use Tiny\RestfulCollection;

class RestfulModel
{
    /**
     * Application ID
     */
    protected $_app_id;

    /**
     * Application Service
     */
    protected $_app_secret;

    /**
     * Model ID name
     */
    protected $_id_column = 'id';

    /**
     * Service host
     */
    protected $_service_host;

    /**
     * Service port
     */
    protected $_service_port;

    /**
     * Base endpoint url
     */
    protected $_base_endpoint;

    /**
     * Target url
     */
    protected $_target_url;

    /**
     * Transaction id
     */
    protected $_transaction;

    /**
     * Request method
     * @var string
     */
    protected $_request_method;

    /**
     * Data collection
     */
    protected $_data_request = [];

    /**
     * Response body accept
     */
    protected $_response_type = 'json';

    /**
     * Is internal request
     * @var boolean
     */
    protected $_is_internal_request;

    /**
     * Response body
     */
    protected $_response_body;

    /**
     * Response header
     */
    protected $_response_header;

    /**
     * Rest transaction manager object
     */
    protected $_transaction_manager;

    /**
     * Rest connection object
     */
    protected $_rest_connection;

    /**
     * Called from transaction rollback action flag
     */
    protected $_is_called_from_transaction = false;

    /**
     * Table relations
     */
    protected $_relations = [];

    /**
     * Current record found count
     */
    protected $_record_count = 0;

    /**
     * Current query parameters
     */
    protected static $_parameters;

    /**
     * Current model instance
     */
    protected static $_model;

    /**
     * Key name for header rollback action
     */
    protected $_header_rollback_key = 'Restful-Rollback-Action';

    /**
     * Header value
     */
    protected static $_header = [];

    /**
     * Always include this relation when using toArray function
     * @var array
     */
    protected $_with_relation = [];

    /**
     * Whitelist when do save or create using mass assignment
     * @var array
     */
    protected $_whitelist = [];

    /**
     * Uri segment params
     * @var array
     */
    protected $_url_segment_keys = [];

    /**
     * Do not include in data request when set property
     * @var boolean
     */
    protected $_skip_from_data_request = false;

    /**
     * Return as single or collection
     * @var boolean
     */
    public static $_return_as_collection = true;

    /**
     * If data is exists this will describe that data source function
     * @var string
     */
    protected static $_data_function_source;

    /**
     * Hide this column
     * @var array
     */
    protected $_system_column = [
        'items', 'pagination'
    ];

    /**
     * Construct function
     */
    public function __construct()
    {
        if (empty($this->_whitelist))
            throw new \Exception("'_whitelist' on class " . get_class($this) . " could not be empty.");

        $this->_whitelist = array_merge($this->_whitelist, [$this->_id_column]);

        // As default all request is from external
        $this->setInternalRequest(false);

        self::$_model[get_called_class()] = $this;

        self::$_return_as_collection = true;
    }

    /**
     * Get model instance
     * @param  string   $key    Model key name
     * @return object
     */
    public static function model($key = null)
    {
        $key = (!empty($key)) ? $key : get_called_class();

        if (isset(self::$_model[$key]))
            return self::$_model[$key];

        return new $key;
    }

    /**
     * Set model object to current var
     * @param string    $key            Model key
     * @param object    $object_model   Model object
     */
    public static function setModel($key, $object_model)
    {
        self::$_model[$key] = $object_model;
    }

    /**
     * Magic function for getter properties
     * @param  string   $property   Property name
     * @return mixed
     */
    public function __get($property)
    {
        if (property_exists($this, $property)){
            return $this->{$property};
        } else if ($this->getDataRequest($property)) {
            return $this->getDataRequest($property);
        }
    }

    /**
     * Magic function for setter properties
     * @param string $property Property name
     * @param mixed  $value    Property value
     * @todo  maybe need some property validation before add it to data request
     */
    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->{$property} = $value;
        } else {
            $this->{$property} = $value;
        }

        // Also append to data request
        if ($this->_skip_from_data_request == false)
        {
            // Check if property is in data request
            if (in_array($property, $this->_whitelist))
            {
                $this->addDataRequest($property, ($value === null) ? "" : $value);
            }
        }
    }

    /**
     * Set column name for ID
     * @param string $column_name Table column name
     */
    public function setIdColumn($column_name)
    {
        $this->_id_column = $column_name;
    }

    /**
     * Get column name for ID
     * @return string
     */
    public function getIdColumn()
    {
        return $this->_id_column;
    }

    /**
     * Set app id
     * @param string $app_id Application id
     */
    public function setAppId($app_id)
    {
        $this->_app_id = $app_id;
    }

    /**
     * Set app secret
     * @param string $app_secret Application secret
     */
    public function setAppSecret($app_secret)
    {
        $this->_app_secret = $app_secret;
    }

    /**
     * Set current value for service host
     * @param string $host Service host url
     */
    public function setServiceHost($host)
    {
        $this->_service_host = $host;
    }

    /**
     * Set current value for service port
     * @param string $port Service port url
     */
    public function setServicePort($port)
    {
        $this->_service_port = $port;
    }

    /**
     * Set response body accept
     * @param string $type Body content type
     */
    public function setResponseType($type)
    {
        $this->_response_type = $type;
    }

    /**
     * Set user auth for access_token replacement
     * @param string $type  User type
     * @param string $user  User ID
     */
    public function setAuth($type, $user)
    {
        $header['User-Type'] = $type;
        $header['User-Id'] = $user;

        self::$_header = $header;

        return $this;
    }

    /**
     * Process query parameters
     * @param  array    $parameters     Collection of query parameters
     * @return void
     */
    protected function _processParameterQuery($parameters)
    {
        $this->appendDataRequest(['query' => $parameters]);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed     $parameters
     * @return Object
     */
    public static function find($parameters = null)
    {
        self::$_parameters = $parameters;

        $caller_model = get_called_class();

        if (!empty(self::model($caller_model)))
        {
            if ($caller_model == get_class(self::model($caller_model)))
                $model = self::model($caller_model);
            else {
                $model = new $caller_model;
            }
        }

        if (empty($model))
            $model = new $caller_model;

        $conditions = isset($parameters['conditions']) ? $parameters['conditions'] : null;

        // This will act as information data source function
        $model::$_data_function_source = $model->_getCaller('function');

        // Check function caller to determine return either Collection or single object
        switch ($model->_getCaller('function'))
        {
            case 'findFirstById':
                $model::$_return_as_collection = false;

                // Get Id from conditions or from bind value
                if (isset($conditions[$model->_id_column]) && !empty($conditions[$model->_id_column]))
                    $model->setExtraUrl($conditions[$model->_id_column]);
                else if (isset($parameters['bind'][$model->_id_column]) && !empty($parameters['bind'][$model->_id_column]))
                    $model->setExtraUrl($parameters['bind'][$model->_id_column]);

                break;

            case 'findFirst':
                $model::$_return_as_collection = false;
                break;

            case 'find':
                $model::$_return_as_collection = true;
                break;

            default:
                # nothing to do
                break;
        }

        // Parse and process queries
        $model->_processParameterQuery($parameters);

        // Generate the url right after model initiate
        $endpoint_uri = $model->generateRequestEndpoint();

        $api = $model->getRestConnect();

        // Send dummy parameters if no parameters is sent
        // Just for laugh
        if (empty($parameters))
            $model->addDataRequest('true', 'false');

        // Send request and store header and body
        $response = $api->sendGet($endpoint_uri, $model->getDataRequest(), self::$_header);

        $model->_processRestfulResponse($response);

        if (!$model->getResponseHeader()->success)
            return $model->getResponseHeader()->success;

        // if just find first then return only one row
        if ($model::$_return_as_collection == false)
        {
            // Assign all data to model property
            $model->assignDataToProperty()->afterFetch($model->getResponseBody());

            // Update model object
            self::setModel($caller_model, $model);

            return $model;
        }

        $resultSet = new RestfulCollection($model->getResponseBody(), $model);

        // Update model object
        self::setModel($caller_model, $model);

        return $resultSet;
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BaseModel
     */
    public static function findFirst($parameters = null)
    {
        self::$_return_as_collection = false;

        $parameters['limit'] = 1;

        return self::find($parameters);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BaseModel
     */
    public static function findFirstById($id)
    {
        self::$_return_as_collection = false;

        return self::find([
            'conditions' => 'id = :id:',
            'bind' => ['id' => $id],
            'limit' => 1
        ]);
    }

    /**
     * Allows to query count result
     *
     * @param mixed $parameters
     * @return mixed
     */
    public static function count($parameters = null)
    {
        $this_parameter = !empty(self::$_parameters) ? self::$_parameters : [];

        if (!empty($parameters))
            self::$_parameters = array_merge_recursive($parameters, $this_parameter);

        $caller_model = get_called_class();
        $result = $caller_model::find(self::$_parameters);

        if ($result)
            return count($result);

        return $result;
    }

    /**
     * Get total current record found
     *
     * @param mixed $parameters
     * @return BaseModel
     */
    public function currentCount()
    {
        return $this->_record_count;
    }

    /**
     * Save function
     * @param  array    $data   Key value data that will be saved to table
     * @return mixed
     */
    public function save($data = [], $whitelist = [])
    {
        throw new \Exception("Please use create or update instead.");
    }

    /**
     * Send create request to restful API
     * @param  array    $data   Key value data that will be saved to table
     * @return boolean
     */
    public function create($data = [], $whitelist = [])
    {
        $whitelist = (!empty($whitelist)) ? array_merge($this->_whitelist, $whitelist) : $this->_whitelist;

        // Append data request
        $this->appendDataRequest($data);

        $this->_data_request = $this->_cleanDataByWhitelist($this->getDataRequest(), $whitelist);

        // Call before update data
        $beforeCreate = $this->beforeCreate($this->_data_request);

        // Exit update if beforeupdate return false
        if ($beforeCreate === false)
            return $beforeCreate;
        else if (is_array($beforeCreate))
            $this->_data_request = $beforeCreate;

        // Get data request just in case the data is already modified on beforeCreate function
        $data = $this->getDataRequest();

        // Add extra header for rollback action info
        if ($this->_is_called_from_transaction)
        {
            self::$_header[$this->_header_rollback_key] = $this->getDataRequest($this->_id_column);
            unset($data[$this->_id_column]);
        }

        $endpoint_uri = $this->generateRequestEndpoint();

        $api = $this->getRestConnect();

        // Send request and store header and body
        $response = $api->sendPost($endpoint_uri, $data, self::$_header);
        $this->_processRestfulResponse($response);

        // Call after create
        if ($this->getResponseHeader()->success)
            $this->assignDataToProperty()->afterCreate($this->getResponseBody());

        return $this->getResponseHeader()->success;
    }

    /**
     * Send update request to restful API
     * @param  array    $data   Key value data that will be saved to table
     * @return boolean
     */
    public function update($data = [], $whitelist = [])
    {
        $whitelist = (!empty($whitelist)) ? array_merge($this->_whitelist, $whitelist) : $this->_whitelist;

        // Append data request
        $this->appendDataRequest($this->_data_request);

        $this->_data_request = $this->_cleanDataByWhitelist($this->getDataRequest(), $whitelist);

        // Call before update data
        $beforeUpdate = $this->beforeUpdate($this->_data_request);

        // Exit update if before update return false
        if ($beforeUpdate === false)
            return $beforeUpdate;
        else if (is_array($beforeUpdate))
            $this->_data_request = $beforeUpdate;

        // Get data request just in case the data is already modified on beforeUpdate function
        $data = $this->getDataRequest();

        switch ($this::$_data_function_source) {
            case 'findFirst':
                // If there is no extra url, then we will append current object id
                if (empty($this->_target_url))
                {
                    if (isset($this->{$this->getIdColumn()}) && !empty($this->{$this->getIdColumn()}))
                        $this->setExtraUrl($this->{$this->getIdColumn()});
                }
                break;

            default:
                # code...
                break;
        }

        // Add extra header for rollback action info
        if ($this->_is_called_from_transaction)
        {
            self::$_header[$this->_header_rollback_key] = md5(time());
        }

        $endpoint_uri = $this->generateRequestEndpoint();

        $api = $this->getRestConnect();

        // Send request and store header and body
        $response = $api->sendPut($endpoint_uri, $data, self::$_header);
        $this->_processRestfulResponse($response);

        if ($this->getResponseHeader()->success)
            $this->assignDataToProperty()->afterUpdate($this->getResponseBody());

        return $this->getResponseHeader()->success;
    }

    /**
     * Delete current object or delete object by given id
     * @param  int  $object_id  Object identifier
     * @return boolean
     */
    public function delete($object_id = null)
    {
        // Call before update data
        $beforeDelete = $this->beforeDelete();

        // Exit update if beforeupdate return false
        if ($beforeDelete === false)
            return $beforeDelete;

        // Append ID to uri request
        if (!empty($object_id))
            $this->setExtraUrl($object_id);
        // else {
        //     if (isset($this->{$this->getIdColumn()}) && !empty($this->{$this->getIdColumn()}))
        //         $this->setExtraUrl($this->{$this->getIdColumn()});
        // }

        // Add extra header for rollback action info
        if ($this->_is_called_from_transaction)
        {
            self::$_header[$this->_header_rollback_key] = md5($object_id);
        }

        $endpoint_uri = $this->generateRequestEndpoint();

        $api = $this->getRestConnect();

        // Send request and store header and body
        $response = $api->sendDelete($endpoint_uri, ['delete' => true], self::$_header);
        $this->_processRestfulResponse($response);

        if ($this->getResponseHeader()->success)
        {
            $this->_appendTransactionState('create', $this->getResponseBody(), get_class($this));

            // Call after delete hook
            $this->assignDataToProperty()->afterDelete($this->getResponseBody());
        }

        return $this->getResponseHeader()->success;
    }

    /**
     * Set current restful transaction id
     * @param object $transaction Restful Transaction Manager
     * @return void
     */
    public function setTransaction(\Tiny\RestfulTransactionsManager $transaction)
    {
        $this->_transaction = $transaction;
    }

    /**
     * Get current restful transaction id
     * @return string
     */
    public function getTransaction()
    {
        return $this->_transaction;
    }

    /**
     * Send current data for transaction purpose
     * @return void
     */
    protected function _appendTransactionState($action, $data, $class_name)
    {
        $transaction = $this->getTransaction();

        if (!empty($transaction) && !$this->_is_called_from_transaction)
        {
            $transaction->appendTransactionSteps(
                $transaction->getTransactionId(),
                $action,
                $data,
                $class_name
            );
        }
    }

    /**
     * Send current data for transaction purpose
     * @param boolean   $bool   Set is this action called from transaction or not
     * @return object
     */
    public function setCalledFromTransaction($bool)
    {
        $this->_is_called_from_transaction = $bool;

        return $this;
    }

    /**
     * Process restful response data
     * @return object
     */
    protected function _processRestfulResponse($response)
    {
        list($body, $header, $method) = $response;

        $this->setResponseBody($body);
        $this->_setResponseHeader($header);
        $this->_request_method = $method;

        // Set count
        $this->_record_count = count($body);

        return $this;
    }

    /**
     * Set current response body
     * @return object
     */
    public function setResponseBody($body)
    {
        if (is_array($body))
            $this->_response_body = (object) $body;
        elseif (is_object($body))
            $this->_response_body = $body;
        else {
            switch ($this->_response_type) {
                case 'json':
                    $result = json_decode($body);

                    if (json_last_error() !== JSON_ERROR_NONE)
                    {
                        throw new \Exception("Body is not valid json : \n\n" . $body, 1);
                    }

                    $this->_response_body = $body;
                    break;

                default:
                    $this->_response_body = $body;
                    break;
            }
        }

        return $this;
    }

    /**
     * Get current response body
     * @return object
     */
    public function getResponseBody()
    {
        return $this->_response_body;
    }

    /**
     * Set current response header
     * @return object
     */
    protected function _setResponseHeader($header)
    {
        if (is_array($header))
            $this->_response_header = (object) $header;
        else
            $this->_response_header = $header;

        return $this;
    }

    /**
     * Get current response header
     * @return object
     */
    public function getResponseHeader()
    {
        return $this->_response_header;
    }

    /**
     * Add header to current request
     * @param string    $key      Header key name
     * @param string    $value    Header value
     * @return object
     */
    public function addRequestHeader($key, $value)
    {
        // $this->_request_header[$key] = $value;
        self::$_header[$key] = $value;

        return $this;
    }

    /**
     * Assign data request
     * @param  array    $data   Collection of data request
     * @return void
     */
    public function assign(array $data)
    {
        $this->_data_request = $data;
    }

    /**
     * Append or merge data request
     * @param  array    $data   Collection of data request
     * @return void
     */
    public function appendDataRequest(array $data)
    {
        $this->_data_request = array_merge($this->_data_request, $data);
    }

    /**
     * Add data request
     * @param  string    $key       Data key
     * @param  string    $value     Data value
     * @return void
     */
    public function addDataRequest($key, $value)
    {
        $this->_data_request[$key] = $value;

        return $this;
    }

    /**
     * Get data request
     * @return array
     */
    public function getDataRequest($key = null)
    {
        if (!empty($key))
        {
            if (isset($this->_data_request[$key]))
                return $this->_data_request[$key];
            else
                return null;
        }

        return $this->_data_request;
    }

    /**
     * Get current error messages on body request
     * @return array
     */
    public function getMessages()
    {
        if (isset($this->getResponseBody()->errors))
            return $this->getResponseBody()->errors;

        return null;
    }

    /**
     * Initiate connection to rest server or service
     * @return object
     */
    public function getRestConnect()
    {
        if (empty($this->_rest_connection))
        {
            $this->_rest_connection = new \Tiny\ApiRequest([
                'app_id' => $this->_app_id,
                'app_secret' => $this->_app_secret,
                'host' => $this->_service_host . ':' . $this->_service_port,
                'hmac' => [
                    'num-first-iterations' => 10,
                    'num-second-iterations' => 10,
                    'num-final-iterations' => 10
                ]
            ]);
        }

        // Check is in header has AccessToken
        if ($access_token = \Phalcon\DI::getDefault()->getRequest()->getHeader('AccessToken'))
            $this->_rest_connection->setAccessToken($access_token);

        return $this->_rest_connection;
    }

    /**
     * Set restfull connection
     * @param mixed     $value  Set current rest connection
     */
    public function setRestConnect($connection = null)
    {
        $this->_rest_connection = $connection;

        return $this;
    }

    /**
     * Sending request data as JSON
     */
    public function sendAsJson()
    {
        $this->getRestConnect()->setDataRequestType('json');

        return $this;
    }

    /**
     * Set extra url that will concat with url prefix
     * @param string    $url    Extra url
     * @param boolean   $url    Override prefix url to empty string
     * @return object
     */
    public function setExtraUrl($url)
    {
        if (!empty($this->_target_url))
            $this->_target_url = trim($this->_target_url, '/') . '/' . $url;
        else
            $this->_target_url = $url;

        return $this;
    }

    /**
     * Generate full endpoint uri request
     * @return string
     */
    public function generateRequestEndpoint()
    {
        $endpoint_prefix = '/' . trim($this->getSource(), '/');
        $endpoint_extra = trim($this->_target_url, '/');

        if (!empty($endpoint_extra))
            $endpoint_prefix .= '/' . $endpoint_extra;

        $url = $this->_processEndpointSegment($endpoint_prefix);

        return $url;
    }

    /**
     * Process endpoint segment
     * @param  string   $segment    Endpoint segment
     * @return string
     */
    protected function _processEndpointSegment($endpoint_url_pattern)
    {
        $parser = new \Tiny\Helper\RouteParser;
        $endpoints = $parser->parse($endpoint_url_pattern);
        $url = "";

        foreach ($endpoints as $segment)
        {
            if (is_array($segment))
            {
                if (count($segment) == 1)
                    $return = $segment[0];
                else {
                    $return = "";

                    foreach ($segment as $value)
                    {
                        if (is_string($value))
                            $return .= $value;
                        else {
                            if (!in_array($value[0], $this->_url_segment_keys))
                                $this->_url_segment_keys[] = $value[0];

                            if (property_exists($this, $value[0]))
                            {
                                // Why the fuck this could be an array
                                if (is_array($this->{$value[0]}))
                                    continue;

                                if (preg_match("(" . $value[1] . ")", $this->{$value[0]}) == true)
                                {
                                    $return .= $this->{$value[0]};
                                }
                            }
                        }
                    }

                    // Make url segment keys unique
                    $this->_url_segment_keys = array_unique($this->_url_segment_keys);
                }
            }

            $url .= $return;
        }

        return $url;
    }

    /**
     * Get model url segment key
     * @param  string   $url    Url pattern
     * @return array
     */
    protected function _getEndpointSegmentKey($url = null)
    {
        if (empty($url))
            $url = $this->getSource();

        if (!empty($this->_url_segment_keys))
            return $this->_url_segment_keys;

        $this->_processEndpointSegment($url);

        return $this->_url_segment_keys;
    }

    /**
     * Clean data by whitelist
     * @param  array    $data       Collection of data
     * @param  array    $whitelist  Collection of whitelist column
     * @return array
     */
    protected function _cleanDataByWhitelist($data, $whitelist = [])
    {
        if (empty($whitelist))
            $whitelist = $this->_whitelist;

        foreach ($data as $key => $value)
        {
            if (!in_array($key, $whitelist))
            {
                if (is_array($data))
                    unset($data[$key]);
                else if (is_object($data))
                    unset($data->{$key});
            }
        }

        return $data;
    }

    /**
     * Clean data based on white list
     * @param  array    $data   Data collection
     * @return mixed
     */
    public function cleanData($data)
    {
        return $this->_cleanDataByWhitelist($data);
    }

    /**
     * Before create event
     * @return bool
     */
    public function beforeCreate()
    {
        return true;
    }

    /**
     * After create event
     * @return bool
     */
    public function afterCreate()
    {
        // Add transaction state
        if ($this->getResponseHeader()->success)
            $this->_appendTransactionState('create', $this->toArray(), get_class($this));

        return true;
    }

    /**
     * Before update event
     * @return bool
     */
    public function beforeUpdate()
    {
        // Add transaction state
        $this->_appendTransactionState('update', $this->toArray(), get_class($this));

        return true;
    }

    /**
     * After update event
     * @return bool
     */
    public function afterUpdate()
    {
        return true;
    }

    /**
     * Before delete event
     * @return bool
     */
    public function beforeDelete()
    {
        // Add transaction state
        $this->_appendTransactionState('delete', $this->getResponseBody(), get_class($this));

        return true;
    }

    /**
     * After delete event
     * @return bool
     */
    public function afterDelete()
    {
        return true;
    }

    /**
     * After fetch event
     * @return bool
     */
    public function afterFetch()
    {
        return true;
    }

    /**
     * Declared has many relation to another restful class
     * @param  string  $relation_name Relation name
     * @param  string  $class_name    Target restful model, must be instance of RestfulModel
     * @param  string  $foreign_key   Column name that acted as fk
     * @return void
     */
    public function hasMany($relation_name, $class_name, $foreign_key)
    {
        $this->_relations[$relation_name] = [
            'type'  => 'has_many',
            'class' => $class_name,
            'fk'    => $foreign_key
        ];
    }

    /**
     * Declared belongs to relation to another restful class
     * @param  string  $relation_name Relation name
     * @param  string  $class_name    Target restful model, must be instance of RestfulModel
     * @param  string  $foreign_key   Column name that acted as fk
     * @return void
     */
    public function belongsTo($relation_name, $class_name, $foreign_key)
    {
        $this->_relations[$relation_name] = [
            'type'  => 'belongs_to',
            'class' => $class_name,
            'fk'    => $foreign_key
        ];
    }

    /**
     * Check is class exists and/or class instance of
     * @param  string $class class name
     * @return boolean
     */
    protected function _checkClassExists($class, $instance_of)
    {
        if (!class_exists($class))
            return false;

        if ($class instanceof RestfulModel)
            return true;

        return false;
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
     * Assign result data to model property, this method could only handle single row result
     * @return object
     * @todo When triggered by findfirst it still check "items" property, maybe services should
     *       check that request only need single object not collection.
     */
    public function assignDataToProperty()
    {
        if (!empty($this->getResponseBody()))
        {
            if (is_array($this->getResponseBody()) || is_object($this->getResponseBody()))
            {
                // For findFirst
                if (isset(self::$_parameters["limit"]) && self::$_parameters["limit"] == 1)
                {
                    if (isset($this->getResponseBody()->items))
                    {
                        $items = reset($this->getResponseBody()->items);

                        if (!empty($items))
                        {
                            foreach ($items as $key => $value)
                            {
                                // Do not include this property to data request
                                $this->_skip_from_data_request = true;

                                $this->{$key} = $value;
                            }
                        }

                        return $this;
                    }
                }

                foreach ($this->getResponseBody() as $key => $value)
                {
                    // Do not include this property to data request
                    $this->_skip_from_data_request = true;

                    $this->{$key} = $value;
                }
            }
        }

        return $this;
    }

    /**
     * Return an array data from request
     * @param  array    $data           Key value data that will be saved to table
     * @param  boolean  $return_object  Return as object instead of array
     * @return mixed
     */
    public function toArray($columns = array(), $return_object = false)
    {
        return $this->_getToArray($columns, $return_object);
    }

    /**
     * Get data and convert to array
     * @param  boolean  $return_object  Return as object instead of array
     * @return array
     */
    protected function _getToArray($columns, $return_object = false)
    {
        // Data collection
        $datas = json_encode($this);
        $datas = json_decode($datas, true);

        if (!empty($columns))
        {
            $new_datas = [];

            foreach ($columns as $column)
            {
                if (isset($datas[$column]))
                    $new_datas[$column] = $datas[$column];
            }

            $datas = $new_datas;
        }

        // Unset hidden column
        if (!empty($this->_system_column))
        {
            $datas = array_diff_key($datas, array_flip($this->_system_column));
        }

        // Unset url segments
        if (!empty($this->_url_segment_keys))
        {
            $datas = array_diff_key($datas, array_flip($this->_url_segment_keys));
        }

        if (!empty($this->_with_relation))
        {
            $datas = $this->_getRelation($this->_with_relation, $datas);
        }

        // cast to object instead of array if needed
        if ($return_object)
            return (object) $datas;

        return $datas;
    }

    /**
     * Get relations
     * @param  array    $parent_relation_info   Parent relations informations
     * @param  array    $sub_relations          Sub relations key
     * @return array
     */
    public function _getRelation($relations = [], $parent_data = [])
    {
        if (!empty($relations))
        {
            foreach ($relations as $key => $relation)
            {
                $relation = $this->parseRestfulRelation($relation);

                // Parsing relation conditions and parameters
                if ($relation instanceof \Tiny\RestfulRelation)
                {
                    $rel = $relation->name;
                    $parameters = $relation;
                    $sub_relation = $relation->getSubRelation();
                } else if ($key instanceof \Tiny\RestfulRelation) {
                    $rel = $key->name;
                    $parameters = $key;
                    $sub_relation = $key->getSubRelation();
                } else {
                    if (is_string($relation))
                    {
                        $rel = $relation;
                        $parameters = \Tiny\RestfulRelation::init($rel);
                        $sub_relation = $relation;
                    } else {
                        $rel = $key;
                        $parameters = \Tiny\RestfulRelation::init($rel);
                        $sub_relation = $relation;
                    }
                }

                if (!isset($this->_relations[$rel]))
                    throw new \Exception("Relation '{$rel}' is not defined");

                $rel_info = $this->_relations[$rel];

                $sub_data = $this->_getRelationData($rel_info, $sub_relation, $parameters);

                $parent_data[$rel] = $sub_data;
            }
        }

        return $parent_data;
    }

    /**
     * Parse restful relation object from array
     * @param  array    $relation   Array of relation object that sent from http request
     * @return mixed
     */
    protected function parseRestfulRelation($relation)
    {
        if (is_array($relation))
        {
            // Check if array has all needed keys
            if (isset($relation['class']) && isset($relation['name']) && isset($relation['parameters']))
            {
                $relation_object = new \Tiny\RestfulRelation($relation['name'], $relation['parameters'], isset($relation['sub_relation']) ? $relation['sub_relation'] : []);

                return $relation_object;
            }
        }

        return $relation;
    }

    /**
     * Get relation data
     * @param  array    $relation       Relation information
     * @param  array    $sub_relations  Sub-Relation information
     * @param  array    $parameters     Relation parameters information
     * @return mixed
     */
    protected function _getRelationData($relation, $sub_relations = [], $parameters = null)
    {
        switch ($relation['type']) {
            case 'belongs_to':
                // Return as object when only need single object
                self::$_return_as_collection = false;

                $model = new $relation['class'];
                $relation_id = $this->{$relation['fk']};

                // if relation id value is empty then why bother
                // to find some record with empty id
                if (empty($relation_id))
                    return null;

                $url_segment_keys = $model->_getEndpointSegmentKey();

                // Assign url segments value
                if (!empty($url_segment_keys))
                {
                    foreach ($url_segment_keys as $key)
                    {
                        if (isset($this->{$key})) {
                            $model->{$key} = $this->{$key};
                        } else if ($relation['fk'] == $key) {
                            $model->{$key} = $this->{$this->getIdColumn()};
                        } else {
                            $model->{$key} = $this->{$key};
                        }
                    }
                }

                $result = $model->findFirstById($relation_id);

                if (!empty($result))
                {
                    $row_data = $result->toArray();

                    if (!empty($sub_relations) && is_array($sub_relations))
                    {
                        $row_data = $result->_getRelation($sub_relations, $row_data);
                    }

                    return $row_data;
                }

                return null;
                break;

            case 'has_many':
                // Return as collection when has many relation
                self::$_return_as_collection = true;

                $return = [];

                $model = new $relation['class'];
                $url_segment_keys = $model->_getEndpointSegmentKey();

                // Assign url segments value
                if (!empty($url_segment_keys))
                {
                    foreach ($url_segment_keys as $key)
                    {
                        if (isset($this->{$key})) {
                            $model->{$key} = $this->{$key};
                        } else if ($relation['fk'] == $key) {
                            $model->{$key} = $this->{$this->getIdColumn()};
                        } else {
                            $model->{$key} = $this->{$key};
                        }
                    }
                }

                if (!$parameters instanceof \Tiny\RestfulQuery)
                    throw new \Exception("Parameters should be instance of 'Tiny\RestfulQuery'");

                $parameters->addCondition("{$relation['fk']} = " . $this->{$this->getIdColumn()});

                // Add flag that this is get data by relation query
                $model->addRequestHeader('Restful-Relation-Request', true);

                $result = $model->find($parameters->getParameters());

                if (!empty($result))
                {
                    foreach ($result as $row)
                    {
                        $row_data = $row->toArray();

                        if (!empty($sub_relations) && is_array($sub_relations))
                        {
                            $row_data = $row->_getRelation($sub_relations, $row_data);
                        }

                        // Unset url segments
                        if (!empty($url_segment_keys))
                        {
                            $row_data = array_diff_key($row_data, array_flip($url_segment_keys));
                        }

                        $return[] = $row_data;
                    }
                }

                return $return;
                break;

            default:
                throw new \Exception(get_class(new self) . " did not support '$relation[type]' relation type");
                break;
        }
    }

    /**
     * Check is current request is success or not
     * @return boolean
     */
    public function isSuccess()
    {
        if (!empty($this->getResponseHeader()))
        {
            if (isset($this->getResponseHeader()->success))
                return $this->getResponseHeader()->success;
        } else {
            if (!empty($this::$_model->getResponseBody()))
            {
                if (isset($this::$_model->getResponseHeader()->success))
                    return $this::$_model->getResponseHeader()->success;
            }
        }

        return false;
    }

    /**
     * Set user access token to current session
     * @param string $access_token user access token
     */
    public function setAccessToken($access_token)
    {
        $this->getRestConnect()->setAccessToken($access_token);

        return $this;
    }

    /**
     * Set user detail to header
     * @param object $user_detail    User detail return from getCurrentUser()
     */
    public function setRequestUser($user_detail)
    {
        if (!empty($user_detail))
        {
            foreach ($user_detail as $key => $value)
            {
                // This will convert snake_case into Header-Naming-Conventions
                $key = ucfirst(str_replace(' ', '-', ucwords(str_replace('_', ' ', $key))));
                $this->addRequestHeader("Users-" . $key, $value);
            }
        }

        return $this;
    }

    /**
     * Set request as internal request
     * @param boolean   $bool       is request from internal or not
     * @param string    $app_id     application id
     */
    public function setInternalRequest($bool, $app_id = null)
    {
        $this->_is_internal_request = $bool;
        $this->addRequestHeader("Is-Internal-Request", $bool);
        $this->addRequestHeader("Internal-Request-App-Id", $app_id);

        return $this;
    }

    /**
     * Get caller detail by using backtrace function
     * @param  string $type Type of caller either "file", "line", "function", "class", "type", or "all"
     * @return [type]       [description]
     */
    protected function _getCaller($type = 'function')
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);

        switch ($type) {
            case 'file':
            case 'line':
            case 'function':
            case 'class':
            case 'type':
                if (isset($trace[2][$type]))
                    return $trace[2][$type];
                else
                    return null;
                break;

            default:
                if (isset($trace[2]))
                    return $trace[2];
                else
                    return [];
                break;
        }
    }
}
