<?php

namespace nullref\useful\actions;

use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\web\BadRequestHttpException;

/**
 * Class MultipleDeleteAction
 *
 * Allows to delete multiple models by primary keys
 *
 * ```php
 *   public function actions()
 *   {
 *       return [
 *           'delete-multiple' => [
 *               'class' => DeleteMultipleAction::className(),
 *               'modelClass' => Type::className(),
 *               'pk' => 'some_id', // primary key for models searhc (default 'id')
 *           ]
 *       ];
 *   }
 * ```
 *
 * or
 * but you should to implement `findModel` in controller class
 * ```php
 *
 *   public function actions()
 *   {
 *       return [
 *           'delete-multiple' => [
 *               'class' => DeleteMultipleAction::className(),
 *               'redirect' => ['index'], // if not set, use Yii::$app->request->referrer
 *               'separator' => ';', // delimiter for ids (default is ',')
 *           ]
 *       ];
 *   }
 * ```
 *
 * @author    Dmytro Karpovych
 * @copyright 2017 NRE
 */
class MultipleDeleteAction extends Action
{
    public $modelClass;

    public $separator = ',';

    public $pk = 'id';

    public $redirect;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if ($this->modelClass) {
            if (!Instance::ensure($this->modelClass, 'yii\db\ActiveRecordInterface')) {
                throw new InvalidConfigException('Model class must implement ActiveRecordInterface');
            }
        } else {
            if (!method_exists($this->controller, 'findModel')) {
                throw new InvalidConfigException('Controller class should have public method findModel');
            }
        }
    }

    /**
     * @param $ids
     */
    public function run($ids)
    {
        $ids = explode($this->separator, $ids);
        if ($this->modelClass) {
            $models = call_user_func([$this->modelClass, 'find'])->where(['id' => $ids])->all();
            foreach ($models as $model) {
                $model->delete();
            }
        } else {
            foreach ($ids as $id) {
                $model = $this->controller->findModel($id);
                $model->delete();
            }
        }
        return $this->controller->redirect($this->redirect ? $this->redirect : Yii::$app->request->referrer);
    }

    /**
     * Check if request has post method
     * @return bool
     * @throws BadRequestHttpException
     */
    protected function beforeRun()
    {
        if (!Yii::$app->request->getIsPost()) {
            throw new BadRequestHttpException();
        }
        return parent::beforeRun();
    }
}