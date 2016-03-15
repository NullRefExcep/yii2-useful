<?php

namespace nullref\useful\traits;

use Yii;

/**
 * @author    Dmytro Karpovych
 * @copyright 2016 NRE
 *
 * Trait GetDefinition
 *
 * @package nullref\core\traits
 *
 * @method string className()
 *
 */
trait GetDefinition
{
    /**
     * Return defined in container class or default
     *
     * @return string
     */
    public static function getDefinitionClass()
    {
        if ($def = Yii::$container->getDefinitions()[static::className()]) {
            return $def['class'];
        }
        return get_called_class();
    }
}