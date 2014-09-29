<?php

namespace nre\behaviors;

use yii\base\Behavior;

/**
 * Class Binary.
 * Behavior for encoding and decoding model fields as integer number.
 *
 * @author    Serhiy Vinichuk <serhiyvinichuk@gmail.com>
 * @copyright 2014 NullReferenceException
 * @license   MIT
 */
class Binary extends Behavior
{
    public $field = [];

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
        $model = $this->getOwner();
        foreach ($this->fields as $field) {
            if (!empty($this->$field)) {
                $model->$field = $this->arrayToBin($this->$field);
            }
        }
    }

    public function decode()
    {
        $model = $this->getOwner();
        foreach ($this->fields as $field) {
            if (!empty($this->$field)) {
                $model->$field = $this->binToArray($this->$field);
            }
        }
    }

    protected function arrayToBin($array)
    {
        $result = 0;
        if (is_array($array)) {
            foreach($array as $key=>$value) 
                $result = $this->set($result, $key, $value);
        }   
        return $result;
    }

    protected function binToArray($value)
    {
        $result = array();
        $s = strrev(decbin($value));
        for($i = 0; $i < strlen($s); $i++) {
            if ($s[$i]==1) {
                $result[] = $i;
            }
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
}