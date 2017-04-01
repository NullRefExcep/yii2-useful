<?php

namespace nullref\useful\traits;

use Yii;
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
 * @method static ActiveQueryInterface find()
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
        $query = static::find();
        if (!empty($condition)) {
            $query->where($condition);
        }
        if ($asArray) {
            $query->asArray();
        }
        return ArrayHelper::map($query->all(), $index, $value);
    }


    /**
     * Return array in [$index => $value] format from records of model
     * With using db component cache
     *
     * @param string $value
     * @param string $index
     * @param array $condition
     * @param bool $asArray
     * @param \yii\db\Connection|null $db
     * @return array
     */
    public static function getCachedMap($value = 'name', $index = 'id', $condition = [], $asArray = true, $db = null)
    {
        $query = static::find();
        if (!empty($condition)) {
            $query->where($condition);
        }
        if ($asArray) {
            $query->asArray();
        }
        if ($db == null) {
            $db = Yii::$app->db;
        }
        $db->cache(function () use ($query, $index, $value) {
            return ArrayHelper::map($query->all(), $index, $value);
        });
    }
}
