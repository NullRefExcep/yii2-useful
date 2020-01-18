<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2020 NRE
 */


namespace app\components;


use Yii;

trait SessionModel
{
    public static function getFromSession($key = null, $safe = true)
    {
        if ($key == null) {
            $key = get_called_class();
        }

        $saved = Yii::$app->session->get($key);
        if (isset($saved)) {
            $model = unserialize($saved);
            return $model;
        }
        if ($safe) {
            $model = new self();
            return $model;
        }
    }

    public function saveToSession($key = null)
    {
        if ($key == null) {
            $key = get_called_class();
        }
        Yii::$app->session->set($key, serialize($this));
    }
}
