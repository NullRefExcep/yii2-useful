<?php

namespace nullref\useful\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * Class JsonBehavior.
 * Behavior for encoding and decoding model fields as JSON.
 *
 * ```php
 *
 * public function behaviors()
 * {
 *     return [
 *          [
 *               'class' => JsonBehavior::className(),
 *               'fields' => ['name_of_field#1', 'name_of_field#2',],
 *          ],
 *     ];
 * }
 *
 * @author    Serhiy Vinichuk <serhiyvinichuk@gmail.com>
 * @copyright 2014 NullReferenceException
 * @license   MIT
 */
class JsonBehavior extends Behavior
{
    public $fields = [];

    public $default = [];

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
                $model->$field = Json::encode($model->$field);
            }
        }
    }

    public function decode()
    {
        $model = $this->owner;
        foreach ($this->fields as $field) {
            $model->$field = empty($model->$field) ? $this->default : Json::decode($model->$field);
        }
    }
} 