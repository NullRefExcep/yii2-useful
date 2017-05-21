<?php

namespace nullref\useful\traits;

use yii\helpers\ArrayHelper;

/**
 * @author    Dmytro Karpovych
 * @copyright 2017 NRE
 *
 * Trait MappableQuery
 *
 * Allows get list from ActiveQuery
 *
 * @package nullref\useful\traits
 *
 * @method array|ActiveRecord[] all(Connection $db)
 */
trait MappableQuery
{
    /**
     * Return array in [$index => $value] format from records of query models
     *
     * @param string $value
     * @param string $index
     * @return array
     */
    public function getMap($value = 'name', $index = 'id')
    {
        return ArrayHelper::map($this->all(), $index, $value);
    }
}
