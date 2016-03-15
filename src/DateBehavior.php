<?php

namespace nullref\useful;

use nullref\useful\behaviors\DateBehavior as BaseDateBehavior;

/**
 * Class DateBehavior.
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
 *               'fields' => ['name_of_field#1', 'name_of_field#1',],
 *               'format' => 'd.m.Y', // Custom datetime format
 *          ],
 *     ];
 * }
 *
 * @author    Serhiy Vinichuk <serhiyvinichuk@gmail.com>
 * @copyright 2014 NullReferenceException
 * @license   MIT
 */
class DateBehavior extends BaseDateBehavior
{
}