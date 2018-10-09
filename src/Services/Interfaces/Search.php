<?php
/**
 * Search interface
 *
 * @author Rolies Deby <rolies106@gmail.com>
 */

namespace Tiny\Services\Interfaces;

interface Search
{
    /**
     * Index document to search engine
     * @param  string   $type   Document type
     * @param  mixed    $id     Document id
     * @param  array    $data   Data that will be indexed
     * @return mixed
     */
    public function create($type, $id = null, $data = []);

    /**
     * Update document to search engine
     * @param  string   $type   Document type
     * @param  mixed    $id     Document id
     * @param  array    $data   Data that will be indexed
     * @return mixed
     */
    public function update($type, $id = null, $data = []);

    /**
     * Delete document from search engine
     * @param  string   $type   Document type
     * @param  mixed    $id     Document id
     * @return mixed
     */
    public function delete($type, $id = null);

    /**
     * Get current search limit per page
     * @return int
     */
    public function getLimit();

    /**
     * Set limit per page
     * @param  int    $id     Set limit per page
     * @return int
     */
    public function setLimit($limit = 10);

    /**
     * Find documents
     * @param object    $query_object   Query object
     * @param int       $page           Get page
     * @param array     $query_extra    Extra function
     * @return object
     */
    public function find($query_object, $page = 1, $query_extra = []);

    /**
     * Find document by ID
     * @param int       $id         Document id
     * @param array     $options    Options
     * @return object
     */
    public function findById($id, array $options);

    /**
     * Get data mapping for search engine
     * @return array
     */
    public function getMapping();

    /**
     * Set data mapping
     * @return array
     */
    public function setMapping(array $mapping);

    /**
     * Set whitelist data
     * @return array
     */
    public function setWhitelist(array $whitelist);
}
