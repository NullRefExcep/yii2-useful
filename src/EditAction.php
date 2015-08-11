<?php

namespace nullref\useful;

use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

/**
 * Action for AJAX record update
 */
class EditAction extends Action
{
    /** @var callable */
    public $findModel;

    /** @var callable */
    public $filter;

    /**
     * Check if action has valid findModel method
     */
    public function init()
    {
        parent::init();
        if (!is_callable($this->findModel)){
            throw new InvalidConfigException('findModel must be set');
        }
    }

    /**
     * Set new attribute value and save AR
     * @throws NotFoundHttpException
     */
    public function run()
    {
        if (!Yii::$app->request->isAjax) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $post = Yii::$app->request->post();

        if (!(empty($post['pk']) || empty($post['name']) || !isset($post['value']))) {
            /** @var ActiveRecord $model */
            $model = call_user_func($this->findModel, $post['pk']);
            $attribute = $post['name'];
            $value = $post['value'];
            if (is_callable($this->filter)) {
                $value = call_user_func($this->filter, $value, $attribute);
            }
            $model->$attribute = $value;
            $model->save();
        }
    }
}