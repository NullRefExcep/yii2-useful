<?php

namespace nullref\useful\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class Date.
 * Behavior for encoding and decoding model fields as Unix timestamp.
 *
 *
 * ```php
 *
 * public function behaviors()
 * {
 *     return [
 *          [
 *               'class' => DateBehavior::className(),
 *               'fields' => ['name_of_field#1', 'name_of_field#2',],
 *               'format' => 'd.m.Y', // Custom datetime format
 *          ],
 *     ];
 * }
 *
 * @author    Serhiy Vinichuk <serhiyvinichuk@gmail.com>
 * @copyright 2014 NullReferenceException
 * @license   MIT
 */
class DateBehavior extends Behavior
{
    public $format = 'd.m.Y';
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
            if (!empty($model->$field) && is_string($model->$field)) {
                $model->$field = strtotime($model->$field);
            }
        }
    }

    public function decode()
    {
        $model = $this->owner;
        foreach ($this->fields as $field) {
            $model->$field = empty($model->$field) ? null : date($this->format, $model->$field);
        }
    }

}