<?php
/**
 * Queue interface
 *
 * @author Rolies Deby <rolies106@gmail.com>
 */

namespace Tiny\Services\Interfaces;

interface Queue
{
    /**
     * Create new document to queue engine
     * @param  string   $id         Document id
     * @param  array    $data       Data that will be indexed
     * @param  string   $operation  Job operation
     * @param  string   $queue      Queue name
     * @return mixed
     */
    public function create($id, $data = [], $operation = 'insert', $queue = 'queue');

    /**
     * Send raw document to queue engine
     * @param  array    $data       Data that will be indexed
     * @param  string   $operation  Job operation
     * @param  string   $queue      Queue name
     * @return mixed
     */
    public function createRaw($data = [], $operation = 'insert', $queue = 'queue');
}
