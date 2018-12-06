<?php

namespace Bridge\Core\Controllers;

use Yii;
use yii\base\Model;
use yii\web\Response;
use Bridge\Core\BridgeModule;
use yii\filters\AccessControl;
use yii\base\InvalidConfigException;
use Zelenin\yii\modules\I18n\Module;
use Zelenin\yii\modules\I18n\models\SourceMessage;
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

    /**
     * @param integer $id
     * @return string|Response
     */
    public function actionUpdate($id)
    {
        /** @var SourceMessage $model */
        $model = $this->findModel($id);
        $model->initMessages();

        if (Model::loadMultiple($model->messages, Yii::$app->getRequest()->post()) && Model::validateMultiple($model->messages)) {
            $model->saveMessages();
            Yii::$app->getSession()->setFlash('success', Module::t('Updated'));
            return $this->redirect(['index']);
        } else {
            return $this->render('update', ['model' => $model]);
        }
    }
}