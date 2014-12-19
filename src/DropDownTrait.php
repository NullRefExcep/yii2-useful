<?php

namespace nullref\useful;

use yii\helpers\ArrayHelper;

/**
 * Allows get list from ActiveRecord
 */
trait DropDownTrait
{
    /**
     * @param string $index
     * @param string $value
     * @param array $condition
     * @param bool $asArray
     * @return array
     */
    public static function getDropDownArray($index = 'id', $value = 'name', $condition = [], $asArray = true)
    {
        $query = static::find()->where($condition);
        if ($asArray) {
            $query->asArray();
        }
        return ArrayHelper::map($query->all(), $index, $value);
    }
}