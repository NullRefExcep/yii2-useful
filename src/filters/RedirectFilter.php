<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2016 NRE
 */


namespace nullref\useful\filters;

/**
 * Redirect after action by url param
 *
 * Usage:
 * ```php
 *
 *   public function behaviors()
 *   {
 *      return [
 *          'redirect' => nullref\useful\filters\RedirectFilter::className(),
 *      ];
 *   }
 *
 *
 */

use Yii;
use yii\base\ActionFilter;

class RedirectFilter extends ActionFilter
{
    public $redirectParam = 'redirect_url';

    public function afterAction($action, $result)
    {
        if ($url = Yii::$app->request->get($this->redirectParam)) {
            return $action->controller->redirect($url);
        }
        return parent::afterAction($action, $result);
    }
}