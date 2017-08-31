<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 6/7/2017
 * Time: 10:33 PM
 */

namespace naffiq\bridge\controllers;


use naffiq\bridge\controllers\actions\DashboardAction;
use naffiq\bridge\models\LoginForm;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class DefaultController extends Controller
{
    public $layout = '@bridge/views/layouts/main';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login'],
                        'roles' => ['?']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ],
            ],
        ];
    }

    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'index' => [
                'class' => DashboardAction::class
            ]
        ]);
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = '@bridge/views/layouts/login';

        $model = new LoginForm();
        if ($model->load(\Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        \Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Uploading image
     *
     * @throws Exception
     */
    public function actionImageUpload()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $uploadedFile = UploadedFile::getInstanceByName('file');
        if (FileHelper::createDirectory(\Yii::getAlias('@webroot/media/tinymce/'))) {
            $fileName = uniqid(time().'_') . '.' . $uploadedFile->extension;
            $uploadedFile->saveAs(\Yii::getAlias('@webroot/media/tinymce/') . $fileName);

            return [
                'location' => \Yii::getAlias('@web/media/tinymce/') . $fileName
            ];
        }

        throw new Exception('Woooowowowow, wait wait wait a minute. Something wrong happened, I need you to debug it.');
    }
}