<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 8/31/17
 * Time: 16:42
 */

namespace Bridge\Core\Controllers\Actions;

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
 * @package Bridge\Core\Controllers\Actions
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