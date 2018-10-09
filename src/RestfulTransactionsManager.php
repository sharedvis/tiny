<?php

namespace Tiny;

/**
 * RestfulTransactionsManager class file
 */

/**
 * Simple class for managing transaction in restful request
 * transaction information will be stored at Redis
 * @author Rolies Deby <rolies106@gmail.com>
 * @package tiny
 */

class RestfulTransactionsManager
{
    /**
     * Transaction key prefix
     * @var string
     */
    protected $_transaction_key_prefix = 'restful_tx_mngr:';

    /**
     * Current transaction id
     * @var string
     */
    protected $_transaction_id;

    /**
     * Current transaction step
     * @var string
     */
    protected $_transaction_step = 0;

    /**
     * Opposite list of every action
     * @var string
     */
    protected $_opposite_action = [
        'create' => 'delete',
        'update' => 'update',
        'delete' => 'create'
    ];

    /**
     * Redis connection state
     * @var object
     */
    protected $_redis;

    public function __construct($host, $port, $db = 0)
    {
        $redis = new \Redis();

        try {
            $connected = $redis->connect($host, $port);
            $redis->select($db);

            $this->_redis = $redis;

            return $this->_redis;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get or generate transcation based on current model
     * @param  \Tiny\RestfulModel $model Model object
     * @return object
     */
    public function get(\Tiny\RestfulModel $model)
    {
        $this->_transaction_id = md5($model->getShortClassName() . time());

        return $this;
    }

    /**
     * Get current transaction ID
     * @return string
     */
    public function getTransactionId()
    {
        return $this->_transaction_id;
    }

    /**
     * Get opposite action taken
     * @param string    $action     Action name
     * @return string
     */
    public function getOppositeAction($action)
    {
        if (isset($this->_opposite_action[$action]))
            return $this->_opposite_action[$action];

        return null;
    }

    /**
     * Append new action step to lists
     * @param  string   $transaction_id     Transaction identifier that created when transaction is initiated
     * @param  string   $action             Action taken
     * @param  array    $data               Array that contain "action" and "data"
     * @param  array    $class_name         Caller class
     * @return void
     */
    public function appendTransactionSteps($transaction_id, $action, $data, $class_name)
    {
        $opposite_action = $this->getOppositeAction($action);

        if (!empty($opposite_action))
            $action = $opposite_action;

        switch ($action) {
            case 'create':
                $data = $data;
                break;

            case 'delete':
            default:
                $data = $data;
                break;
        }

        // Put step
        $this->_redis->hMSet(
            $this->_transaction_key_prefix . $transaction_id,
            [$this->_transaction_step => json_encode(['action' => $action, 'class' => $class_name, 'data' => $data])]
        );

        // Increase transaction step
        $this->_transaction_step++;
    }

    /**
     * Commit transaction, thats mean all history will be truncated
     * @return void
     */
    public function commit()
    {
        $this->_redis->delete($this->_transaction_key_prefix . $this->_transaction_id);
    }

    /**
     * Rollback all action on restful transaction
     * @return void
     */
    public function rollback()
    {
        $actions = $this->_redis->hGetAll($this->_transaction_key_prefix . $this->_transaction_id);
        $valid_rollback = true;

        if (!empty($actions))
        {
            foreach ($actions as $key => $value)
            {
                $value = json_decode($value);

                switch ($value->action) {
                    case 'delete':
                        // Try to delete by id
                        $model_object = new $value->class;

                        if (isset($value->data->{$model_object->_id_column}))
                        {
                            $success = $model_object->setCalledFromTransaction(true)->delete($value->data->{$model_object->_id_column});

                            if ($success) {
                                unset($actions->{$key});
                            } else {
                                $valid_rollback = false;
                            }
                        }
                        break;

                    case 'update':
                        // Try to delete by id
                        $model_object = new $value->class;

                        if (isset($value->data->{$model_object->_id_column}))
                        {
                            $model_object->setCalledFromTransaction(true)
                                         ->setExtraUrl($value->data->{$model_object->_id_column});

                            $success = $model_object->setCalledFromTransaction(true)->update((array) $value->data);

                            if ($success) {
                                unset($actions->{$key});
                            } else {
                                $valid_rollback = false;
                            }
                        }
                        break;

                    case 'create':
                        // Try to delete by id
                        $model_object = new $value->class;

                        $model_object->setCalledFromTransaction(true);

                        $success = $model_object->setCalledFromTransaction(true)->create((array) $value->data);

                        if ($success) {
                            unset($actions->{$key});
                        } else {
                            $valid_rollback = false;
                        }
                        break;

                    default:
                        # code...
                        break;
                }
            }

            // If there is something wrong happened on rollback
            // system will keep the redis record
            //
            // @TODO : need more flawless handler
            if (!$valid_rollback)
            {
                // Delete current state first
                $this->_redis->delete($this->_transaction_key_prefix . $this->_transaction_id);

                // Reinsert rest data
                if (!empty($actions))
                {
                    foreach ($actions as $key => $value) {
                        $this->_redis->hMSet(
                            $this->_transaction_key_prefix . $this->_transaction_id,
                            [$key => $value]
                        );
                    }
                }
            }
        }
    }
}
