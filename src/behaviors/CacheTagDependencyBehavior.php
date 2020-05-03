<?php

namespace app\behaviors;

use Yii;
use yii\base\Behavior;
use yii\caching\TagDependency;
use yii\db\ActiveRecord;

/**
 * Class CacheTagDependencyBehavior
 *
 * Behavior for invalidation TagDependency when modify records
 *
 * ```php
 *
 * public function behaviors()
 * {
 *     return [
 *          [
 *               'class' => CacheTagDependencyBehavior::class,
 *               'tags' => self::CACHE_KEY,
 *          ],
 *     ];
 * }
 *
 * @package nullref\useful\behaviors
 *
 * @author    Dmytro Karpovych <ZAYEC77@gmail.com>
 * @copyright 2020 NullReferenceException
 * @license   MIT
 */
class CacheTagDependencyBehavior extends Behavior
{
    /**
     * @var string|array|callable a list of tag names for this dependency.
     * You could pass callable if you need to generate tags dynamically
     */
    public $tags;

    /**
     * @var \yii\caching\CacheInterface | null
     * If empty `Yii::$app->cache` will used
     */
    public $cache;

    /**
     * @return array|string[]
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_DELETE => 'invalidate',
            ActiveRecord::EVENT_AFTER_INSERT => 'invalidate',
            ActiveRecord::EVENT_AFTER_UPDATE => 'invalidate',
        ];
    }

    /**
     *
     */
    public function invalidate()
    {
        TagDependency::invalidate($this->cache ?? Yii::$app->cache, $this->getTags());
    }

    /**
     * @return mixed
     */
    protected function getTags()
    {
        if (is_callable($this->tags)) {
            return call_user_func($this->tags);
        }
        return $this->tags;
    }
}
