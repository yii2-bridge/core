<?php

namespace Bridge\Core\Controllers;

use Bridge\Core\BridgeModule;
use yii\filters\AccessControl;
use yii\base\InvalidConfigException;
use Zelenin\yii\modules\I18n\controllers\DefaultController as BaseI18nController;

class I18nController extends BaseI18nController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => $this->getAdminModule()->allowedRoles,
                    ],
                ],
            ],
        ];
    }

    /**
     * Gets bridge module instance
     *
     * @return BridgeModule
     * @throws InvalidConfigException
     */
    protected function getAdminModule()
    {
        /** @var $module BridgeModule */
        $module = \Yii::$app->getModule('admin');

        if (!$module) {
            throw new InvalidConfigException('Module \'admin\' is not set in app, please configure it for further usage');
        }

        if (!$module instanceof BridgeModule) {
            throw new InvalidConfigException('Module \'admin\' should be instance of BridgeModule');
        }

        return $module;
    }
}