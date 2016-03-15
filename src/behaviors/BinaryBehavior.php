<?php

namespace nullref\useful\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class Binary.
 * Behavior for encoding and decoding model fields as integer number.
 * e.g.: [1, 0, 1, 1, 0](array) => 13(int)
 *
 *
 * ```php
 *
 * public function behaviors()
 * {
 *     return [
 *          [
 *               'class' => BinaryBehavior::className(),
 *               'fields' => ['name_of_field#1', 'name_of_field#2',],
 *          ],
 *     ];
 * }
 *
 * @author    Serhiy Vinichuk <serhiyvinichuk@gmail.com>
 * @author    Dmytro Karpovych <ZAYEC77@gmail.com>
 * @copyright 2016 NullReferenceException
 * @license   MIT
 */
class BinaryBehavior extends Behavior
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
            if (!empty($this->$field)) {
                $model->$field = $this->arrayToBin($this->$field);
            }
        }
    }

    /**
     * Convert array to int
     * @param $array
     * @return int
     */
    protected function arrayToBin($array)
    {
        $result = 0;
        if (is_array($array)) {
            foreach ($array as $key => $value)
                $result = $this->set($result, $key, $value);
        }
        return $result;
    }

    protected function set($object, $pos, $value)
    {
        if (empty($value))
            return $object & ~(1 << $pos);
        else
            return $object | (1 << $pos);
    }

    public function decode()
    {
        $model = $this->owner;
        foreach ($this->fields as $field) {
            if (!empty($this->$field)) {
                $model->$field = $this->binToArray($this->$field);
            }
        }
    }

    /**
     * Convert int to array
     * @param $value
     * @return array
     */
    protected function binToArray($value)
    {
        $result = array();
        $s = strrev(decbin($value));
        for ($i = 0; $i < strlen($s); $i++) {
            if ($s[$i] == 1) {
                $result[] = $i;
            }
        }
        return $result;
    }
}