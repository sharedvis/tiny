<?php
/**
 * Model pagination handler collection
 *
 * @author Rolies Deby <rolies106@gmail.com>
 */

namespace Tiny\Traits;

use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Phalcon\Paginator\Adapter\NativeArray as PaginatorArray;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;

/**
 * Model Pagination Trait
 */
trait ModelPaginationTrait
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
     * Set current max pagination limit
     * @param int   $limit  Maximum per page limit
     * @return int
     */
    public function setMaxLimit($limit)
    {
        $this->_max_limit = $limit;

        return $this;
    }

    /**
     * Set limit and from from current elastica query
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
     * Get curent pagination info
     *
     * @return array Pagination information
     */
    public function getPagination($paginator = null)
    {
        // If this is phalcon model paginator
        if (!empty($paginator) && ($paginator instanceof PaginatorModel || $paginator instanceof PaginatorArray || $paginator instanceof PaginatorQueryBuilder)) {
            $page = $paginator->getPaginate();
            return [
                'page' => $page->current,
                'total_pages' => $page->total_pages,
                'total_items' => $page->total_items,
                'per_page' => $this->getLimit(),
                'has_next' => ($page->current < $page->total_pages),
                'has_previous' => ($page->current > 1 && $page->total_pages > 1)
            ];
        } else if (!empty($paginator) && get_class($paginator) == 'stdClass') {
            return [
                'page' => $paginator->current,
                'total_pages' => $paginator->total_pages,
                'total_items' => $paginator->total_items,
                'per_page' => $paginator->limit,
                'has_next' => ($paginator->current < $paginator->total_pages),
                'has_previous' => ($paginator->current > 1 && $paginator->total_pages > 1)
            ];
        }
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

    /**
     * Alter query to include limit and offset
     * @param  [type] $query [query array]
     * @param  [type] $page
     * @param  [type] $limit
     * @return [type] $query [altered]
     */
    public function prepareManualPagination ($query, $page, $limit)
    {

        $count = $this->count($query);

        if(!isset($query['order'])){
            $query['order'] = "id asc";
        }

        $query['limit'] = $limit;
        $query['offset'] = $page <= 1 ? 0 : ($page - 1) * $limit;

        $pagination = $query;
        $pagination['count'] = $count;
        $pagination['page'] = $page;

        return [
            'pagination' => $pagination,
            'query' => $query,
        ];

    }


    /**
     * Create manual pagination
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function createManualPagination($data)
    {
        $total_page = ceil($data['count'] / $data['limit']);
        $result = [
            'first' => 1,
            'per_page' => $data['limit'],
            'limit' => $data['limit'],
            'current' => $data['page'],
            'page' => $data['page'],
            'total_items' => $data['count'],
            'last' => $total_page,
            'total_pages' => $total_page,
            'has_next' => ($data['page'] < $total_page) ? true : false,
            'has_previous' => ($data['page'] > 1) ? true : false,

        ];

        $data['page'] > 1 ? $result['before'] = $data['page'] - 1 : '';
        $data['page'] < $total_page ? $result['next'] = $data['page'] + 1 : '';

        return $result;
    }
}
