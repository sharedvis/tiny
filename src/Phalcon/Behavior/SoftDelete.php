<?php

namespace Tiny\Phalcon\Behavior;

use Phalcon\Mvc\Model\Behavior\SoftDelete as PhalconSoftDelete;

/**
 * Phalcon softdelete extend
 *
 * Instead of permanently delete a record it marks the record as
 * deleted changing the value of a flag column, but if there is header
 * for rollback action it'll permanently delete the record
 *
 * @see https://github.com/phalcon/cphalcon/blob/1.2.6/ext/mvc/model/behavior/softdelete.c
 */
class SoftDelete extends PhalconSoftDelete
{
    /**
     * Listens for notifications from the models manager
     *
     * @param string $type
     * @param \Phalcon\Mvc\ModelInterface $model
     * @throws Exception
     */
    public function notify($type, \Phalcon\Mvc\ModelInterface $model)
    {
        if ($type === 'beforeDelete') {
            if (!$model->getDI()->getRequest()->getHeader('Restful-Rollback-Action'))
            {
                // Set deleted_at property
                if (property_exists($model, 'deleted_at'))
                    $model->deleted_at = \Tiny\Helper\Time::toUtc(date("Y-m-d H:i:s"));
                
                $parent_result = parent::notify($type, $model);

                // If parent action is failed then return imediately
                if ($parent_result === false)
                    return $parent_result;

                // call afterDelete
                if (method_exists($model, 'afterDelete'))
                    $model->afterDelete();

                return $parent_result;
            }
        }
    }
}
