<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 8/31/17
 * Time: 16:42
 */

namespace naffiq\bridge\controllers\actions;

use naffiq\bridge\BridgeModule;
use yii\base\Action;
use yii\helpers\Url;
use yii\httpclient\Client;
use yii\web\Cookie;

/**
 * Class LanguageSwitchAction
 *
 * Storing language in cookies
 *
 * @package naffiq\bridge\controllers\actions
 */
class LanguageSwitchAction extends Action
{
    public function run($lang, $returnUrl = null)
    {
        \Yii::$app->response->cookies->add(new Cookie([
            'name' => 'lang',
            'value' => $lang
        ]));

        if ($returnUrl) {
            \Yii::$app->user->setReturnUrl($returnUrl);
        }

        return $this->controller->goBack($returnUrl);
    }

    /**
     * @return BridgeModule|mixed
     */
    protected function getModule()
    {
        return $this->controller->module;
    }
}