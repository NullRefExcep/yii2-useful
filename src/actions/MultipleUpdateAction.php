<?php

namespace nullref\useful\actions;

use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use yii\web\Response;

/**
 * Class MultipleUpdateAction
 *
 * Allows to update multiple models by primary keys separated from POST
 *
 * ```php
 *   public function actions()
 *   {
 *       return [
 *           'update-multiple' => [
 *               'class' => MultipleUpdate::className(),
 *               'modelClass' => Type::className(),
 *           ]
 *       ];
 *   }
 *
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 */
class MultipleUpdateAction extends Action
{
    public $modelClass;

    public $separator = ',';

    public $pk = 'id';

    public function init()
    {
        parent::init();
        if (!Instance::ensure($this->modelClass, 'yii\db\ActiveRecordInterface')) {
            throw new InvalidConfigException('Model class must implement ActiveRecordInterface');
        }
    }

    public function run($ids)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }
        $ids = explode($this->separator, $ids);
        $models = call_user_func([$this->modelClass, 'find'])->where(['id' => $ids])->all();
        /** @var ActiveRecord[] $models */
        $models = ArrayHelper::index($models, $this->pk);
        if (Model::loadMultiple($models, Yii::$app->request->post()) && Model::validateMultiple($models)) {
            foreach ($models as $model) {
                $model->save();
            }
            return $models;
        }
        return $models;
    }
}