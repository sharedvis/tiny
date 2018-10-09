<?php
/**
 * Soft Create Behavior
 *
 * @author Rolies Deby <rolies106@gmail.com>
 * @version 0.1.0
 * @package Tiny
*/
namespace Tiny\Phalcon\Behavior;

use \Phalcon\Mvc\Model\Behavior;
use \Phalcon\Mvc\Model\BehaviorInterface;
use \Phalcon\Mvc\Model\Exception;
use \Phalcon\Mvc\ModelInterface;

/**
 * Tiny\Phalcon\Behavior\SoftCreate
 *
 * Instead create new record, this behavior will check if there is
 * the record already exists and will just change the status
 *
 * Note : primary key for reverted record must be sent on create request
 */
class SoftCreate extends Behavior implements BehaviorInterface
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
        if (is_string($type) === false ||
            is_object($model) === false ||
            $model instanceof ModelInterface === false) {
            throw new Exception('Invalid parameter type.');
        }

        if ($type === 'beforeCreate')
        {
            $options = $this->getOptions();

            if (isset($options['field']) === false) {
                throw new Exception("The options 'field' is required");
            }

            //Skip the current operation
            $model->skipOperation(true);

            $id = $model->getDI()->getRequest()->getHeader('Restful-Rollback-Action');
            $actualValue = $model->readAttribute($options['field']);

            $updateModel = clone $model;

            // Check if record exists
            $exists = $updateModel->findFirstById($id);

            //If the record is already flagged as default value we don't update it again
            if (!empty($exists) && $exists->{$options['field']} !== $actualValue)
            {
                //Update the cloned model
                $exists->writeAttribute($options['field'], $actualValue);
                if ($exists->save() !== true) {
                    //Transfer the message from the cloned model to the original model
                    $messages = $exists->getMessages();
                    foreach ($messages as $message) {
                        $model->appendMessage($message);
                    }

                    return false;
                }

                // Get model primary key column
                $metaData = new \Phalcon\Mvc\Model\MetaData\Memory();
                $key = $metaData->getPrimaryKeyAttributes($model);

                //Update the original model too
                $model->writeAttribute($options['field'], $actualValue);
                $model->writeAttribute(reset($key), $exists->{reset($key)});

                // Abort record creation
                return false;
            }
        }
    }
}
