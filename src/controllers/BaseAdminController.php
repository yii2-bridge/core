<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 6/5/2017
 * Time: 1:11 AM
 */

namespace Bridge\Core\Controllers;

use Bridge\Core\BridgeModule;
use yii\base\InvalidConfigException;
use yii2tech\admin\CrudController;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class BaseAdminController extends CrudController
{
    /**
     * @var string
     */
    public $layout = '@bridge/views/layouts/main';

    /**
     * @var array|null contains roles, that have access to the controller. If value is `null`, then
     * it will be initialized from `Bridge\Core\BridgeModule` config.
     * Default value: `['admin']`
     */
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
                'roles' => $this->allowedRoles,
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

        if (!$module instanceof BridgeModule) {
            throw new InvalidConfigException('Module \'admin\' should be instance of BridgeModule');
        }

        return $module;
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete-file' => ['post'],
                ],
            ],
        ]);
    }

    /**
     * Удаление файла
     *
     * @param int $id ID записи
     * @param string $modelName Название класса модели, с полным путем (с namespace)
     * @param string $behaviorName Название поведения загрузки файла/изображения
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDeleteFile(int $id, string $modelName, string $behaviorName = 'imageUpload')
    {
        $model = $modelName::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        $imageBehavior = $model->getBehavior($behaviorName);

        /**
         * Удаляем сам файл.
         * Если мы удаляем изображение, то удаляется и их превью.
         */
        $imageBehavior->afterDelete();

        /**
         * Удаляем запись файла в самом модели
         */
        $model->{$imageBehavior->attribute} = null;
        $model->detachBehaviors();

        return $model->save(false);
    }
}