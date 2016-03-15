<?php

namespace nullref\useful\traits;

use yii\db\ActiveQueryInterface;
use yii\helpers\ArrayHelper;

/**
 * @author    Dmytro Karpovych
 * @copyright 2016 NRE
 *
 * Trait Mappable
 *
 * Allows get list from ActiveRecord
 *
 * @package nullref\useful\traits
 *
 * @method ActiveQueryInterface find()
 */
trait Mappable
{
    /**
     * Return array in [$index => $value] format from records of model
     *
     * @param string $value
     * @param string $index
     * @param array $condition
     * @param bool $asArray
     * @return array
     */
    public static function getMap($value = 'name', $index = 'id', $condition = [], $asArray = true)
    {
        $query = static::find()->where($condition);
        if ($asArray) {
            $query->asArray();
        }
        return ArrayHelper::map($query->all(), $index, $value);
    }

    //@TODO implement method with cache
}