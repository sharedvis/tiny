<?php

/**
 * Query builder for elasticsearch
 * this class is for helping builder an elasticsearch query
 */

namespace Tiny\Services\Helper\ElasticSearch;

class QueryBuilder
{
    /**
     * Where or condition
     * @var array
     */
    protected $_where_or = [];

    /**
     * Where like or condition
     * @var array
     */
    protected $_where_like_or = [];

    /**
     * Where not condition
     * @var array
     */
    protected $_where_not = [];

    /**
     * Where and condition
     * @var array
     */
    protected $_where_and = [];

    /**
     * Where like and condition
     * @var array
     */
    protected $_where_like_and = [];

    /**
     * Where in condition
     * @var array
     */
    protected $_where_in = [];

    /**
     * Where range condition
     * @var array
     */
    protected $_where_range = [];

    /**
     * Has parent
     * @var array
     */
    protected $_has_parent = [];

    /**
     * Has child
     * @var array
     */
    protected $_has_child = [];

    /**
     * Inner hits
     * @var array
     */
    protected $_inner_hits = [];

    /**
     * Has column
     * @var array
     */
    protected $_field_exists = null;

    /**
     * Order By
     * @var array
     */
    protected $_order = [];

    /**
     * Limit
     * @var int
     */
    protected $_limit_per_page = 20;

    /**
     * Page
     * @var int
     */
    protected $_page = 1;

    protected $_multi_match = [];

    /**
     * Add or where condition
     * @param  string $column    Column name
     * @param  string $condition Column value
     * @return object
     */
    public function orWhere($column, string $condition)
    {
        if (is_array($column))
            $column = implode(',', $column);

        $this->_where_or[$column] = $condition;

        return $this;
    }

    /**
     * Add and where condition
     * @param  string $column    Column name
     * @param  string $condition Column value
     * @return object
     */
    public function andWhere($column, string $condition)
    {
        if (is_array($column))
            $column = implode(',', $column);

        $this->_where_and[$column] = $condition;

        return $this;
    }

    /**
     * Add and where condition
     * @param  string $column    Column name
     * @param  string $condition Column value
     * @return object
     */
    public function notWhere($column, string $condition)
    {
        if (is_array($column))
            $column = implode(',', $column);

        $this->_where_not[$column] = $condition;

        return $this;
    }

    /**
     * Add where in condition
     * @param  string $column    Column name
     * @param  mixed  $condition Column value, either array or string separated by comma
     * @return object
     */
    public function inWhere(string $column, array $condition)
    {
        $this->_where_in[$column] = $condition;

        return $this;
    }

    /**
     * Add range where condition
     * @param  string $column    Column name
     * @param  mixed  $from      Column value from
     * @param  mixed  $to        Column value to
     * @return object
     */
    public function rangeWhere(string $column, $from = null, $to = null)
    {
        $this->_where_range = [$column => [$from, $to]];

        return $this;
    }

    /**
     * Add order by
     * @param  string $column   Column name
     * @param  mixed  $order    Order sorting
     * @return object
     */
    public function order(string $column, $order = 'asc')
    {
        $this->_order[$column] = $order;

        return $this;
    }

    /**
     * Add check column exists
     * @param  string $column   Column name
     * @return object
     */
    public function fieldExists(string $column)
    {
        $this->_field_exists = $column;

        return $this;
    }

    /**
     * Add has parent
     * @param  string $parent_type   Parent type
     * @param  mixed  $query         Query
     * @return object
     */
    public function hasParent(string $parent_type, $query = [])
    {
        $this->_has_parent[$parent_type] = $query;

        return $this;
    }

    /**
     * Add has child
     * @param  string $child_type    Child type
     * @param  mixed  $query         Query
     * @return object
     */
    public function hasChild(string $child_type, $query = [])
    {
        $this->_has_child[$child_type] = $query;

        return $this;
    }

    /**
     * Add inner hits
     * @param  string $name     Inner hits name
     * @param  array  $options  Inner hits options
     * @return object
     */
    public function innerHits(string $name, $options = [])
    {
        $this->_inner_hits[$name] = $options;

        return $this;
    }

    /**
     * Set limit
     * @param  int  $limit   Limit per page
     * @return object
     */
    public function setLimit(int $limit)
    {
        $this->_limit_per_page = $limit;

        return $this;
    }

    /**
     * Set page
     * @param  int  $page   Set current page
     * @return object
     */
    public function setPage(int $page)
    {
        $this->_page = $page;

        return $this;
    }

    /**
     * Set multi match query
     * @param  array    $column      array of column names
     * @param  string   $condition   query condition
     * @param  string   $type        multi_match query types (best_fields | most_fields | cross_fields | phrase | phrase_prefix)
     * @param  string   $operator
     * @return object
     */
    public function multiMatch($column, string $condition, $extras = [])
    {
        $multi_match = [];
        $multi_match[] = [
            "condition" => $condition,
            "column" => $column,
        ];

        if (!empty($extras)) {
            foreach ($multi_match as $i => $multi) {
                foreach ($extras as $j => $value) {
                    switch ($j) {
                        case "type":
                            $multi_match[$i]["type"] = $value;
                            break;
                        
                        case "operator":
                            $multi_match[$i]["operator"] = $value;
                            break;

                        case "fuzziness":
                            $multi_match[$i]["fuzziness"] = $value;
                            break;

                        default:
                            continue;
                    }
                }
            }
        }

        $this->_multi_match = array_merge($this->_multi_match, $multi_match);

        return $this;
    }

    /**
     * Get all conditions and merge it to one array so
     * it'll easily sent to rest api
     * @return array
     */
    public function toArray()
    {
        $return = [
            'where_or' => $this->_where_or,
            'where_like_or' => $this->_where_like_or,
            'where_and' => $this->_where_and,
            'where_not' => $this->_where_not,
            'where_like_and' => $this->_where_like_and,
            'where_in' => $this->_where_in,
            'where_range' => $this->_where_range,
            'multi_match' => $this->_multi_match,
            'has_parent' => $this->_has_parent,
            'field_exists' => $this->_field_exists,
            'has_child' => $this->_has_child,
            'inner_hits' => $this->_inner_hits,
            'order' => $this->_order,
            'limit' => $this->_limit_per_page,
            'page' => $this->_page,
        ];

        return $return;
    }

    /**
     * Load all properties from user array query
     * @return object
     */
    public function fromArray(array $query)
    {
        if (!empty($query))
        {
            foreach ($query as $key => $value)
            {
                switch ($key) {
                    case 'where_or':
                        $this->_where_or = $value;
                        break;

                    case 'where_like_or':
                        $this->_where_like_or = $value;
                        break;

                    case 'where_not':
                        $this->_where_not = $value;
                        break;

                    case 'where_and':
                        $this->_where_and = $value;
                        break;

                    case 'where_like_and':
                        $this->_where_like_and = $value;
                        break;

                    case 'where_in':
                        $this->_where_in = $value;
                        break;

                    case 'where_range':
                        $this->_where_range = $value;
                        break;

                    case 'field_exists':
                        $this->_field_exists = $value;
                        break;

                    case 'has_parent':
                        $this->_has_parent = $value;
                        break;

                    case 'has_child':
                        $this->_has_child = $value;
                        break;

                    case 'inner_hits':
                        $this->_inner_hits = $value;
                        break;

                    case 'order':
                        $this->_order = $value;
                        break;

                    case 'limit':
                        $this->_limit_per_page = $value;
                        break;

                    case 'page':
                        $this->_page = $value;
                        break;

                    case 'multi_match':
                        $this->_multi_match = $value;

                    default:
                        // Do nothing, just go to the next value
                        continue;
                        break;
                }
            }
        }

        return $this;
    }

    /**
     * Process array of query to elastic query
     * @param  array  $query Collection of query condition
     * @return object
     */
    public function processToElastic($query = null)
    {
        if (empty($query))
            $query = $this;

        if (is_object($query))
        {
            if ($query instanceof \Tiny\Services\Helper\ElasticSearch\QueryBuilder)
            {
                $query = $query->toArray();
            }
        }

        // Basic Query
        $query_elastic = $this->elasticQuery('BoolQuery');
        $query_args = [];

        if (isset($query['where_or']) && !empty($query['where_or']))
        {
            foreach ($query['where_or'] as $column => $value)
            {
                if (!empty($value))
                {
                    $column = explode(",", $column);
                    $query_or = $this->elasticQuery();
                    $query_or->setFields($column);
                    $query_or->setQuery(\Elastica\Util::replaceBooleanWordsAndEscapeTerm($value));
                    $query_elastic->addMust($query_or);
                }
            }
        }

        if (isset($query['where_and']) && !empty($query['where_and']))
        {
            foreach ($query['where_and'] as $column => $value)
            {
                if (!empty($value))
                {
                    $column = explode(",", $column);
                    $query_and = $this->elasticQuery();
                    $query_and->setFields($column);
                    $query_and->setQuery(\Elastica\Util::replaceBooleanWordsAndEscapeTerm($value));
                    $query_elastic->addMust($query_and);
                }
            }
        }

        if (isset($query["multi_match"]) && !empty($query["multi_match"])) {

            foreach ($query["multi_match"] as $key => $value) {
                if (!empty($value)) {
                    $query_multi_match = $this->elasticQuery("MultiMatch");
                    $query_multi_match->setQuery($value["condition"]);
                    $query_multi_match->setFields($value["column"]);

                    if (isset($value["type"]) && !empty($value["type"])) {
                        $query_multi_match->setType($value["type"]);    
                    }
                    
                    if (isset($value["operator"]) && !empty($value["operator"])) {
                        $query_multi_match->setOperator($value["operator"]);    
                    }

                    if (isset($value["fuzziness"])) {
                        $query_multi_match->setFuzziness($value["fuzziness"]);
                    }

                    $query_elastic->addMust($query_multi_match);
                }
            }

        }

        if (isset($query['where_not']) && !empty($query['where_not']))
        {
            foreach ($query['where_not'] as $column => $value)
            {
                $column = explode(",", $column);
                $query_not = $this->elasticQuery();
                $query_not->setFields($column);
                $query_not->setQuery(\Elastica\Util::replaceBooleanWordsAndEscapeTerm($value));
                $query_elastic->addMustNot($query_not);
            }
        }

        if (isset($query['where_in']) && !empty($query['where_in']))
        {
            foreach ($query['where_in'] as $column => $value)
            {
                $query_terms = $this->elasticQuery('Terms');
                $query_terms->setTerms($column, (array) $value);
                $query_elastic->addMust($query_terms);
            }

            $query['where_in'] = [];
        }

        if (isset($query['where_like_or']) && !empty($query['where_like_or']))
        {
            foreach ($query['where_like_or'] as $column => $value)
            {
                $query_like = $this->elasticQuery();
                $query_like->setFields([$column]);
                $query_like->setQuery(\Elastica\Util::replaceBooleanWordsAndEscapeTerm($value));
                $query_elastic->addMust($query_like);
            }
        }

        if (isset($query['where_like_and']) && !empty($query['where_like_and']))
        {
            foreach ($query['where_like_and'] as $column => $value)
            {
                $query_like = $this->elasticQuery();
                $query_like->setFields([$column]);
                $query_like->setQuery(\Elastica\Util::replaceBooleanWordsAndEscapeTerm($value));
                $query_elastic->addMust($query_like);
            }
        }

        if (isset($query['where_range']) && !empty($query['where_range']))
        {
            foreach ($query['where_range'] as $column => $value)
            {
                $range = [];

                if (isset($value[0]) && $value[0] !== null)
                    $range['gte'] = $value[0];

                if (isset($value[1]) && $value[1] !== null)
                    $range['lte'] = $value[1];

                if (!empty($range))
                {
                    $query_range = $this->elasticQuery('Range');
                    $query_range->addField($column, $range);

                    $query_elastic->addMust($query_range);
                }
            }
        }

        if (isset($query['order']) && !empty($query['order']))
        {
            $sorting = [];

            foreach ($query['order'] as $column => $order)
            {
                $sorting[] = [
                    $column => [
                        'order' => $order,
                        'missing' => '_last',
                        // 'missing' => PHP_INT_MAX -1,
                    ]
                ];
            }

            $query_args['setSort'] = [$sorting];
        }

        if (isset($query['field_exists']) && !empty($query['field_exists']))
        {
            $query_exists = $this->elasticQuery('Exists', [$query['field_exists']]);
            $query_elastic->addMust($query_exists);
        }

        if (isset($query['has_parent']) && !empty($query['has_parent']))
        {
            foreach ($query['has_parent'] as $parent_type => $query_p)
            {
                $queryBuilderParent = new QueryBuilder;
                $queryBuilderParent->fromArray($query_p);

                list($query_object_parent, $args_parent) = $queryBuilderParent->processToElastic();

                $query_parent = $this->elasticQuery('HasParent', [$query_object_parent, $parent_type]);

                $query_elastic->addMust($query_parent);
            }
        }

        if (isset($query['has_child']) && !empty($query['has_child']))
        {
            foreach ($query['has_child'] as $child_type => $query_c)
            {
                $queryBuilderChild = new QueryBuilder;
                $queryBuilderChild->fromArray($query_c);

                list($query_object_child, $args) = $queryBuilderChild->processToElastic();

                $query_child = $this->elasticQuery('HasChild', [$query_object_child, $child_type]);

                // Set child inner hits
                if (isset($query_c['inner_hits']) && !empty($query_c['inner_hits']))
                {
                    $innerHits = $this->elasticQuery('InnerHits');
                    $innerHits->setName($child_type);

                    if (isset($query_c['order']) && !empty($query_c['order']))
                    {
                        $sorting_c = [];

                        foreach ($query_c['order'] as $column => $order)
                        {
                            $sorting_c[] = [
                                $column => [
                                    'order' => $order,
                                    'missing' => '_last',
                                    // 'missing' => PHP_INT_MAX -1,
                                ]
                            ];
                        }

                        $innerHits->setSort($sorting_c);
                    }

                    // Just get at least first 1000 records if limit is not the default
                    if (isset($query_c['limit']) && $query_c['limit'] != $this->_limit_per_page)
                        $innerHits->setSize($query_c['limit']);
                    else {
                        $innerHits->setSize(1000);
                    }

                    $query_child->setInnerHits($innerHits);
                }

                $query_elastic->addMust($query_child);
            }
        }

        if (isset($query['limit']) && !empty($query['limit']))
        {
            $query_args['setSize'] = [$query['limit']];
        }

        return [$query_elastic, $query_args];
    }

    /**
     * Initiate elastica query class
     * @param  string $type Query class that supported by elastica
     * @param  array  $args Query class construct arguments
     * @return object
     */
    public function elasticQuery($type = 'QueryString', $args = [])
    {
        $class = '\Elastica\Query\\' . $type;

        if (!class_exists($class))
            throw new \Phalcon\Exception('Class ' . $class . ' did not exists');

        if (empty($args))
            $instance = new $class;
        else {
            $reflection = new \ReflectionClass($class);
            $instance = $reflection->newInstanceArgs($args);
        }

        return $instance;
    }


    /**
     * Initiate elastica aggregation class
     * @param string $type Aggregation class that supported by elastica
     * @param string $name Aggregation name
     * @return object
     */
    public function elasticAggs($type = 'Terms', $name = null)
    {
        $class = '\Elastica\Aggregation\\' . $type;

        if (!class_exists($class))
            throw new \Phalcon\Exception('Class ' . $class . ' did not exists');

        $instance = new $class($name);

        return $instance;
    }
}
