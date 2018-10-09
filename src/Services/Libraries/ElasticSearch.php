<?php
/**
 * Elasticsearch
 *
 * @author Rolies Deby <rolies106@gmail.com>
 */

namespace Tiny\Services\Libraries;

use \Elastica\Type\Mapping;
use \Elastica\Document;
use \Elastica\Client;
use \Elastica\Request;
use \Elastica\Search;
use \Elastica\Query;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Phalcon\Paginator\Adapter\NativeArray as PaginatorArray;

/**
 * Elasticsearch
 */
class ElasticSearch implements \Tiny\Services\Interfaces\Search
{
    /**
     * Max limit allowed
     * @var integer
     */
    protected $_max_limit = 100;

    /**
     * Result limit
     * @var integer
     */
    protected $_limit = 25;

    /**
     * Total found item
     * @var int
     */
    protected $_total_items = 0;

    /**
     * Total found pages
     * @var int
     */
    protected $_total_pages = 0;

    /**
     * Current page position
     * @var int
     */
    protected $_current_page = 0;

    /**
     * Is elastic scoring enabled
     * @var boolean
     */
    protected $_enable_scoring = false;

    /**
     * Scoring parameter
     * @var array
     */
    protected $_scoring_params = [];

    /**
     * Exclude data before sending to elasticsearch
     * @var array
     */
    protected $_elastic_data_exclude = [];

    /**
     * Default value for elastic query cache lifetime
     * @var integer
     */
    protected $_elastic_query_cache_lifetime = 3600;

    /**
     * Elasticsearch index
     * @var string
     */
    protected $_elastic_index = 'elasticsearch';

    /**
     * Elasticsearch type
     * @var string
     */
    protected $_elastic_type = 'type';

    /**
     * Number of shards
     * @var integer
     */
    protected $_number_of_shards = 3;

    /**
     * Number of replicas
     * @var integer
     */
    protected $_number_of_replicas = 2;

    /**
     * Data mapping
     * @var array
     */
    protected $_mapping = [];

    /**
     * Connection object
     * @var integer
     */
    protected $_connection;

    /**
     * Parent search object
     * @var integer
     */
    protected $_search;

    /**
     * Whitelist data
     * @var array
     */
    protected $_whitelist;

    /**
     * Document parent
     * @var array
     */
    protected $_parent;

    /**
     * Model
     * @var string
     */
    protected $_model;

    /**
     * Document parent fk column
     * @var array
     */
    protected $_parent_fk_column;

    /**
     * Document routing fk column
     * @var array
     */
    protected $_routing_fk_column;

    /**
     * Aggregations
     * @var array
     */
    protected $_aggregations;

    /**
     * Document childs
     * @var array
     */
    protected $_childs = [];

    /**
     * Elasticsearch options
     * @var array
     */
    protected $_elastic_search_options = [];

    public function __construct(array $connection_information, $search_object)
    {
        $this->_search = $search_object;

        if (isset($connection_information['index']))
            $this->_elastic_index = $connection_information['index'];

        if (empty($this->_connection))
            $this->_connection = new Client($connection_information);
    }

    /**
     * Add elasticsearch document child
     * @return object
     */
    public function addChild($model, $type)
    {
        $this->_childs[$model] = $type;

        return $this;
    }

    /**
     * Get childs
     * @return array
     */
    public function getChilds()
    {
        return $this->_childs;
    }

    /**
     * Set elasticsearch document parent
     * @return object
     */
    public function setParent($parent_type, $parent_column_id)
    {
        $this->_parent = $parent_type;
        $this->_parent_fk_column = $parent_column_id;

        return $this;
    }

    /**
     * Set elasticsearch document routing column name
     * @return object
     */
    public function setRoutingColumn($routing_column_name)
    {
        $this->_routing_fk_column = $routing_column_name;

        return $this;
    }

    /**
     * Elasticsearch mapping
     * @return array
     */
    public function getMapping()
    {
        return $this->_mapping;
    }

    /**
     * Set elasticsearch mapping
     * @return array
     */
    public function setMapping(array $mapping)
    {
        $this->_mapping = $mapping;

        return $this->_mapping;
    }

    /**
     * Get current pagination limit
     * @return int
     */
    public function getLimit()
    {
        return $this->_limit;
    }

    /**
     * Set current pagination limit
     * @param int   $limit  Current pagination limit
     * @return int
     */
    public function setLimit($limit = 10)
    {
        if ($limit <= $this->_max_limit)
            $this->_limit = $limit;
        else
            $this->_limit = $this->_max_limit;

        return $this;
    }

    /**
     * Set search engine type
     * @param string $type Search engine type
     */
    public function setType($type)
    {
        $this->_elastic_type = $type;
    }

    /**
     * Set model
     * @param string $model Model name
     */
    public function setModel($model)
    {
        $this->_model = $model;

        return $this;
    }

    /**
     * Index document to elasticsearch
     * @param  int      $id     Document id
     * @param  array    $data   Document content
     * @return void
     */
    public function create($type, $id = null, $data = [])
    {
        if (empty($id))
            throw new \Exception("Document id is required");

        if (empty($this->_whitelist))
            throw new \Exception("You must set data whitelist column");

        $data = $this->_search->cleanDataByWhitelist($data, $this->_whitelist);
        $data = $this->_search->cleanDataTypeByMapping($data, $this->getMapping());

        // First parameter is the id of document.
        $doc = new Document($id, $data);

        $this->_elasticaChildType($type);

        $elasticaType = $this->_elasticaType($type);

        if (!empty($this->_parent))
        {
            if (isset($data[$this->_parent_fk_column]))
                $doc->setParent($data[$this->_parent_fk_column]);
            else
                throw new \Exception("Key parent '{$this->_parent_fk_column}'' is not found in " . print_r($data, true) . ".");
        }

        if (!empty($this->_routing_fk_column))
        {
            if (isset($data[$this->_routing_fk_column]))
                $doc->setRouting($data[$this->_routing_fk_column]);
            else
                throw new \Exception("Key parent '{$this->_routing_fk_column}'' is not found in " . print_r($data, true) . ".");
        }

        // Add document to type
        $elasticaType->addDocument($doc);

        // Refresh Index
        $elasticaType->getIndex()->refresh();
    }

    /**
     * Index bulk documents to elasticsearch
     * @param  string   $type   Document type
     * @param  array    $data   Document content
     * @return void
     */
    public function createBulk($type, $datas = [])
    {
        if (empty($this->_whitelist))
            throw new \Exception("You must set data whitelist column");

        if (!empty($datas))
        {
            // Populate data to document
            $documents = $this->populateDocument($datas);

            // Mapping fields
            $this->_elasticaChildType($type);

            $elasticaType = $this->_elasticaType();

            // Mapping fields
            // $this->_elasticaMapping($elasticaType);

            $elasticaType->addDocuments($documents);

            // Refresh Index
            $elasticaType->getIndex()->refresh();
        }
    }

    /**
     * Populate data to documents
     * @param  array    $data   Document content
     * @return array
     */
    private function populateDocument($data)
    {
        $documents = [];

        foreach ($data as $item)
        {
            $id = (int) $item['id'];

            $item = $this->_search->cleanDataByWhitelist($item, $this->_whitelist);
            $item = $this->_search->cleanDataTypeByMapping($item, $this->getMapping());

            $doc = new Document(
                $id,
                $item
            );

            if (!empty($this->_parent))
            {
                if (isset($item[$this->_parent_fk_column]))
                {
                    $doc->setParent($item[$this->_parent_fk_column]);
                } else {
                    throw new \Exception("Key {$this->_parent_fk_column} is not found in data.");
                }
            }

            if (!empty($this->_routing_fk_column))
            {
                if (isset($item[$this->_routing_fk_column]))
                    $doc->setRouting($item[$this->_routing_fk_column]);
                else
                    throw new \Exception("Key parent '{$this->_routing_fk_column}'' is not found in " . print_r($item, true) . ".");
            }

            $documents[] = $doc;
        }

        return $documents;
    }

    /**
     * Update document to elastic
     * @param  string   $type   Document type
     * @param  array    $data   Document content
     * @return void
     */
    public function update($type, $id = null, $data = [])
    {
        if (!empty($id) && !empty($data))
        {
            if (empty($id))
                throw new \Exception("Document id is required");

            if (empty($this->_whitelist))
                throw new \Exception("You must set data whitelist column");

            // Set document type
            $this->setType($type);
            $options = [];
            $elasticaType = $this->_elasticaType();
            $doc = new Document($id, $data);

            if (!empty($this->_parent))
            {
                if (isset($data[$this->_parent_fk_column])) {
                    $doc->setParent($data[$this->_parent_fk_column]);
                    $options['parent'] = $data[$this->_parent_fk_column];
                }
                else
                    throw new \Exception("Key {$this->_parent_fk_column} is not found in data.");
            }

            if (!empty($this->_routing_fk_column))
            {
                if (isset($data[$this->_routing_fk_column]))
                    $doc->setRouting($data[$this->_routing_fk_column]);
                else
                    throw new \Exception("Key parent '{$this->_routing_fk_column}'' is not found in " . print_r($data, true) . ".");
            }

            $data = $this->_search->cleanDataByWhitelist($data, $this->_whitelist);
            $data = $this->_search->cleanDataTypeByMapping($data, $this->getMapping());

            try {
                $doc_exists = $elasticaType->getDocument($id, $options);
            } catch (\Elastica\Exception\ResponseException $e) {
                $doc_exists = null;
            } catch (\Exception $e) {
                $doc_exists = null;
            }

            if ($doc_exists) {
                // Add update document
                $elasticaType->updateDocument($doc);
            } else {
                // Add update document
                $this->create($type, $id, $data);
            }
        }
    }

    /**
     * Delete document from elastic
     * @param  string    $type   Document type
     * @param  integer   $id     Document id
     * @return void
     */
    public function delete($type, $id = null, $data = [])
    {
        if (!is_null($id))
        {
            // Get document type
            $elasticaType = $this->_elasticaType($type);
            $options = [];

            if (!empty($this->_routing_fk_column))
            {
                if (isset($data[$this->_routing_fk_column]))
                    $options['routing'] = $data[$this->_routing_fk_column];
                else
                    throw new \Exception("Key parent '{$this->_routing_fk_column}'' is not found in " . print_r($data, true) . ".");
            }

            try {
                $doc_exists = $elasticaType->getDocument($id, $options);
            } catch (\Exception $e) {
                $doc_exists = null;
            }

            // Delete document
            if (!empty($doc_exists)) {
                $elasticaType->deleteById($id, $options);
            }
        }
    }

    /**
     * Find any document
     * @param object    $query_object   Elastica query object that produced by elasticQuery()
     * @param int       $page           Current page
     * @param array     $query_extra    Extra function that will called by Elastica Query class
     * @return object
     */
    public function find($query_object, $page = 1, $query_extra = [])
    {
        if (!is_object($query_object))
            throw new \Phalcon\Exception('First arg should be an object from elasticQuery()');

        // Create a "global" query
        $query = new Query;

        if (!empty($this->_model))
        {
            if (method_exists($this->_model, 'beforeFindSearchEngine'))
            {
                list($query_modified, $query_object_modified) = call_user_func([$this->_model, 'beforeFindSearchEngine'], $query, $query_object);

                if (!is_object($query_object_modified) || !is_object($query_modified))
                    throw new \Phalcon\Exception('beforeFindSearchEngine should return Elastica Query object.');

                $query = $query_modified;
                $query_object = $query_object_modified;
            }
        }

        // Add query_string query to "global" query
        $query->setQuery($query_object);

        if (is_array($query_extra) && !empty($query_extra))
        {
            foreach ($query_extra as $method => $args)
            {
                switch ($method) {
                    case 'setSize':
                        $this->setLimit(reset($args));
                        break;

                    default:
                        if (method_exists($query, $method))
                        {
                            $query = call_user_func_array(array($query, $method), $args);
                        }
                        break;
                }
            }
        }

        // Add aggregations to current query
        if (!empty($this->_aggregations))
        {
            foreach ($this->_aggregations as $aggs)
            {
                $query->addAggregation($aggs);
            }
        }

        $client = new Client();

        $index = $this->_elasticaIndex();

        $type = $index->getType($this->_elastic_type);

        list($type, $query) = $this->setPagination($type, $query, $page, $this->getLimit());

        if (env('ELASTIC_QUERY_CACHE_ENABLE') == 'true')
            $result = $this->_elasticQueryCache($type, $query);
        else
            $result = $type->search($query, $this->_getElasticSearchOptions());

        return $result;
    }

    /**
     * Find document by id
     * @param int   $id     Document ID
     * @return object
     */
    public function findById($id, array $options = [])
    {
        // Create a "global" query
        $query = new Query;

        $client = new Client();

        $index = $this->_elasticaIndex();

        $type = $index->getType($this->_elastic_type);

        try {
            $document = $type->getDocument($id, $options);
        } catch (Exception $e) {
            $document = null;
        }

        return $document;
    }

    /**
     * Set elasticsearch pagination information
     * @param object  $type     Elastica\Type object
     * @param object  $query    Elastica\Query object
     * @param integer $page     Destination page
     *
     * @return array
     */
    public function setPagination($type, $query, $page = 1)
    {
        $this->_current_page = $page;
        $this->_total_items = $type->count($query);
        $this->_total_pages = (int) ceil($this->_total_items / $this->getLimit());
        $from = (($this->_current_page * $this->getLimit())) - $this->getLimit();

        $query->setSize($this->getLimit());
        $query->setFrom($from);

        return [$type, $query];
    }

    /**
     * Get pagination info
     *
     * @return array Pagination information
     */
    public function getPagination($paginator = null)
    {
        // return elasticsearch paginator
        return [
            'page' => $this->_current_page,
            'total_pages' => $this->_total_pages,
            'total_items' => $this->_total_items,
            'per_page' => $this->getLimit(),
            'has_next' => ($this->_current_page < $this->_total_pages),
            'has_previous' => ($this->_current_page > 1 && $this->_total_pages > 1)
        ];
    }

   // /**
   //   * Update elastic by query
   //   * @param  string $type     Elasticsearch type
   //   * @param  array  $query    Phalcon model query data
   //   * @return void
   //   */
   //  public function updateByQuery($type, $query = [])
   //  {
   //      switch ($type)
   //      {
   //          case 'products':
   //              $products = \Bromo\Models\Jayawijaya\Products::find($query);
   //              $data = [];
   //              if (!empty($products))
   //              {
   //                  foreach ($products as $product)
   //                  {
   //                      $data[] = $product->with(['company' => ['membership'], 'category', 'images', 'unit', 'type'], 'update')->toArray();
   //                  }
   //                  $this->insertDocument($type, $data);
   //              }
   //              $this->logProcess("complete batch update of " . count($data) . " " . $type);
   //              break;
   //          default:
   //              $this->logProcess("batch update for " . $type . " is not supported.");
   //              break;
   //      }
   //  }

    // /**
    //  * Initiate elastica aggregation class
    //  * @param string $type Aggregation class that supported by elastica
    //  * @param string $name Aggregation name
    //  * @return object
    //  */
    // public function elasticAggs($type = 'Terms', $name = null)
    // {
    //     $class = '\Elastica\Aggregation\\' . $type;

    //     if (!class_exists($class))
    //         throw new \Phalcon\Exception('Class ' . $class . ' did not exists');

    //     $instance = new $class($name);

    //     return $instance;
    // }

    // /**
    //  * Initiate elastica query dsl, previousely using filter
    //  * @param string $type      Filter type
    //  * @param string $param     Class parameter
    //  * @return object
    //  */
    // public function elasticQueryDsl($type = 'Terms', $param = null)
    // {
    //     $class = '\Elastica\Query\\' . $type;

    //     if (!class_exists($class))
    //         throw new \Phalcon\Exception('Class ' . $class . ' did not exists');

    //     if (!empty($param))
    //         $instance = new $class($param);
    //     else
    //         $instance = new $class;

    //     return $instance;
    // }

    // /**
    //  * Set current scoring to random, all scoring will be overwrite
    //  */
    // public function setRandomScoring()
    // {
    //     $this->_scoring_params = [
    //         'random_score' => rand(1, 10000)
    //     ];
    // }

    // /**
    //  * Set option for elasticsearch
    //  * @param string    $key    Elasticsearch key name
    //  * @param string    $value  Option value
    //  */
    // public function setElasticSearchOption($key, $value = null)
    // {
    //     $this->_elastic_search_options[$key] = $value;

    //     return $this;
    // }

    /**
     * Set aggregations
     * @param array $aggregations Collection of aggregations instances
     */
    public function setAggregations($aggregations = [])
    {
        $this->_aggregations = $aggregations;

        return $this;
    }

    /**
     * Set whitelist data
     * @param array $whitelist
     */
    public function setWhitelist(array $whitelist)
    {
        $this->_whitelist = $whitelist;

        return $this;
    }

    /**
     * Create or get index from elasticsearch
     * @return object
     */
    protected function _elasticaIndex()
    {
        // Load index
        $elasticaIndex = $this->_connection->getIndex($this->_elastic_index);

        // Just return the index if already exists
        if ($elasticaIndex->exists())
            return $elasticaIndex;

        // Create the index new
        $elasticaIndex->create(
            [
                'number_of_shards' => $this->_number_of_shards,
                'number_of_replicas' => $this->_number_of_replicas,
                'analysis' => [
                    'analyzer' => [
                        'indexAnalyzer' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => ['lowercase', 'substring']
                        ],
                        'searchAnalyzer' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => ['standard', 'lowercase']
                        ]
                    ],
                    'filter' => [
                        "substring" => [
                            "type" => "nGram",
                            "min_gram" => 1,
                            "max_gram"  => 10
                        ],
                        // 'mySnowball' => [
                        //     'type' => 'snowball',
                        //     'language' => 'English'
                        // ]
                    ]
                ]
            ]
        );

        return $elasticaIndex;
    }

    /**
     * Create or get elasticsearch type
     * @return object
     */
    protected function _elasticaType($type = null, $options = [])
    {
        if (empty($type))
            $type = $this->_elastic_type;

        $elasticaIndex = $this->_elasticaIndex();

        //Create a type
        $elasticaType = $elasticaIndex->getType($type);

        // Just return the type if already exists
        if ($elasticaType->exists())
            return $elasticaType;

        // Create mapping for type
        $this->_elasticaMapping($elasticaType, $options);

        return $elasticaType;
    }

    /**
     * Create or get elasticsearch child type
     * @return array
     */
    protected function _elasticaChildType($parent, $childs = [])
    {
        $child_types = [];

        if (empty($childs))
            $childs = $this->_childs;

        if (!empty($childs))
        {
            foreach ($childs as $model => $type)
            {
                $model_object = new $model;

                // @TODO : this is not generic method, only tweak for phalcon model
                // need to think better way than this
                // if using "onConstruct" at model will reach maximum deep of DI
                if (method_exists($model_object, 'initialize'))
                    $model_object->initialize();

                $sub_childs = $model_object->getSearchEngine()->getChilds();

                if (!empty($sub_childs))
                    $this->_elasticaChildType($type, $sub_childs);

                $mapping = (method_exists($model_object, "getSearchEngineMapping")) ? $model_object->getSearchEngineMapping() : [];

                $child_types[] = $this->_elasticaType($type, [
                    'parent' => $parent,
                    'mapping' => $mapping
                ]);
            }
        }

        return $child_types;
    }

    /**
     * Mapping elasticsearch
     * @return object
     */
    protected function _elasticaMapping($elasticaType, $options = [])
    {
        // Define mapping
        $mapping = new Mapping();
        $mapping->setType($elasticaType);
        $model_map = $this->getMapping();

        // Mapping more options
        foreach ($options as $key => $value)
        {
            switch ($key) {
                case 'parent':
                    $mapping->setParent($value);
                    break;

                case 'mapping':
                    $model_map = $value;
                    break;

                default:
                    // Nothing to do here
                    continue;
                    break;
            }
        }

        // $mapping->setParam('analyzer', ['type' => 'standard']);

        // Define boost field
        // $mapping->setParam('_boost', array('name' => '_boost', 'null_value' => 1.0));

        // Set mapping
        $properties = array_merge(
            ['id' => ['type' => 'long', 'include_in_all' => true]],
            $model_map
        );

        $mapping->setProperties($properties);

        // Send mapping to type
        $mapping->send();
    }

    // /**
    //  * Processing elastic scoring function
    //  * @param  object   $query  Elastica FunctionScore object
    //  * @return object
    //  */
    // protected function _elasticScoring($query = null)
    // {
    //     if (empty($query))
    //         $query = new ElasticSearchFunctionScore;
    //     else {
    //         if (!($query instanceof \Elastica\Query\FunctionScore))
    //             throw new \Phalcon\Exception("Query must be function score instance.");
    //     }

    //     $query->setBoostMode(\Elastica\Query\FunctionScore::BOOST_MODE_SUM);
    //     $query->setScoreMode(\Elastica\Query\FunctionScore::SCORE_MODE_SUM);

    //     foreach ($this->_scoring_params as $key => $value)
    //     {
    //         if ($key == 'random_score') {
    //             $query->addRandomScoreFunction($value);
    //         } else {
    //             foreach ($value['columns'] as $column => $column_value)
    //             {
    //                 if (isset($value['type'])) {
    //                     switch ($value['type'])
    //                     {
    //                         case 'Exists':
    //                             $filter = $this->elasticQueryDsl($value['type'], $column_value);
    //                             break;

    //                         default:
    //                             # code...
    //                             break;
    //                     }
    //                 } else {
    //                     $filter = $this->elasticQueryDsl('Term');
    //                     $filter->setTerm($column, $column_value);
    //                 }

    //                 $query->addFunction(null, null, $filter, $value['weight']);
    //             }
    //         }
    //     }

    //     return $query;
    // }

 //    /**
 //     * Process query and get result from cache if exists
 //     * @param  object $type  Current request type object
 //     * @param  object $query Current request query object
 //     * @param  string $value [description]
 //     * @return mixed
 //     */
 //    protected function _elasticQueryCache($type, $query)
 //    {
 //        $redis = $this->getDI()->get('redis');
 //        $key = $this->_elasticCacheKeyGenerate($type, $query);
 //        $cache_lifetime = env('ELASTIC_QUERY_CACHE_LIFETIME', $this->_elastic_query_cache_lifetime);

 //        // Get cache from redis
 //        $result = $redis->get($key);

 //        // Return cache if exists
 //        if (!empty($result))
 //            return unserialize($result);

 //        // Get from elasticsearch if cache not exists and save it to redis
 //        $result = $type->search($query, $this->_getElasticSearchOptions());

 //        $redis->set($key, serialize($result), ['nx', 'ex' => $cache_lifetime]);

 //        return $result;
 //    }

 //    /**
 //     * Generate cache key for current elasticsearch request
 //     * @param  object   $type   Elastica Type object
 //     * @param  object   $query  Elastica Query object
 //     * @return string
 //     */
 //    protected function _elasticCacheKeyGenerate($type, $query)
 //    {
 //        $type_name = $type->getName();
 //        $params = $query->getParams();
 //        $params['query'] = $query->getQuery()->toArray();

 //        return md5($type_name . '-' . json_encode($params));
 //    }

	/**
     * Get options for elastic search
     * @return array
     */
    protected function _getElasticSearchOptions()
    {
        $default = [
            // Search::OPTION_SCROLL => '1m'
        ];

        return array_merge($default, $this->_elastic_search_options);
    }
}
