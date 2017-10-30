<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 6/5/2017
 * Time: 1:11 AM
 */

namespace naffiq\bridge\controllers;

use naffiq\bridge\BridgeModule;
use yii\base\InvalidConfigException;
use yii2tech\admin\CrudController;

class BaseAdminController extends CrudController
{
    /**
     * @var string
     */
    public $layout = '@bridge/views/layouts/main';

    public $allowedRoles = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->allowedRoles === null) {
            $this->allowedRoles = $this->getAdminModule()->allowedRoles;
        }
    }

    /**
     * @return array
     */
    public function accessRules()
    {
        return [
            [
                'allow' => true,
                'roles' => ['admin'],
            ],
        ];
    }

    /**
     * Gets bridge module instance
     *
     * @return BridgeModule
     * @throws InvalidConfigException
     */
    protected function getAdminModule() {
        /** @var $module BridgeModule */
        $module = \Yii::$app->getModule('admin');

        if (!$module) {
            throw new InvalidConfigException('Module \'admin\' is not set in app, please configure it for further usage');
        }

        if ($module instanceof BridgeModule) {
            throw new InvalidConfigException('Module \'admin\' should be instance of BridgeModule');
        }

        return $module;
    }
}