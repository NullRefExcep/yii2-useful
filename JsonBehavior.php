<?php

namespace nre\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * Class JsonBehavior.
 * Behavior for encoding and decoding model fields as JSON.
 *
 * @author    Serhiy Vinichuk <serhiyvinichuk@gmail.com>
 * @copyright 2014 NullReferenceException
 * @license   MIT
 */
class JsonBehavior extends Behavior
{
    public $fields;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'arrayToJson',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'arrayToJson',
            ActiveRecord::EVENT_AFTER_FIND => 'jsonToArray',
            ActiveRecord::EVENT_AFTER_INSERT => 'jsonToArray',
            ActiveRecord::EVENT_AFTER_UPDATE => 'jsonToArray',
        ];
    }

    public function arrayToJson()
    {
        $model = $this->owner;
        foreach ($this->fields as $field) {
            if (isset($model->$field)) {
                $model->$field = Json::encode($model->$field);
            }
        }
    }

    public function jsonToArray()
    {
        $model = $this->owner;
        foreach ($this->fields as $field) {
            $model->$field = empty($model->$field) ? [] : Json::decode($model->$field);
        }
    }
} 