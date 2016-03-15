<?php

namespace nullref\useful\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class SerializeBehavior.
 * Behavior for encoding and decoding model fields as storable representation of a value.
 *
 * ```php
 *
 * public function behaviors()
 * {
 *     return [
 *          [
 *               'class' => SerializeBehavior::className(),
 *               'fields' => ['name_of_field#1', 'name_of_field#2',],
 *          ],
 *     ];
 * }
 *
 * @author    Tenfrow <vit2zremb@gmail.com>
 * @copyright 2016 NullReferenceException
 * @license   MIT
 */
class SerializeBehavior extends Behavior
{
    public $fields = [];

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'encode',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'encode',
            ActiveRecord::EVENT_AFTER_FIND => 'decode',
            ActiveRecord::EVENT_AFTER_INSERT => 'decode',
            ActiveRecord::EVENT_AFTER_UPDATE => 'decode',
        ];
    }

    public function encode()
    {
        $model = $this->owner;
        foreach ($this->fields as $field) {
            if (isset($model->$field)) {
                $model->$field = serialize($model->$field);
            }
        }
    }

    public function decode()
    {
        $model = $this->owner;
        foreach ($this->fields as $field) {
            $model->$field = unserialize($model->$field);
        }
    }
} 