<?php

namespace nullref\helpers;

/**
 * Class with db utils
 * 
 * @author    Dmytro Karpovych
 * @copyright 2017 NRE
 */
class Db
{
    /**
     * Extract attribute from dsn string
     * e.g:
     * ```php
     *      $host = Db::getDsnAttribute('host', Yii::$app->db->dsn);
     * ```
     * @param $name
     * @param $dsn
     * @return null|string
     */
    public static function getDsnAttribute($name, $dsn)
    {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        }
        return null;
    }
}
