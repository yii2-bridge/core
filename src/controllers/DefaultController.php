<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 6/7/2017
 * Time: 10:33 PM
 */

namespace Bridge\Core\Controllers;


use Bridge\Core\BridgeModule;
use Bridge\Core\Models\LoginForm;
use Bridge\Core\Widgets\Toastr;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

class DefaultController extends Controller
{
    public $layout = '@bridge/views/layouts/main';

    /**
     * @var BridgeModule
     */
    public $module;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['login', 'logout', 'set-menu-state', 'index', 'clear-cache', 'switch-language'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login'],
                        'roles' => ['?']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['set-menu-state'],
                        'roles' => ArrayHelper::merge(['?'], $this->module->allowedRoles)
                    ],
                    [
                        'allow' => true,
                        'actions' => ['logout', 'index', 'clear-cache', 'switch-language'],
                        'roles' => $this->module->allowedRoles
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'index' => [
                'class' => $this->module->dashboardAction
            ],
            'switch-language' => [
                'class' => $this->module->languageSwitchAction
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
     * ElFinder file manager
     *
     * @return string
     */
    public function actionElfinder()
    {
        return $this->render('elfinder');
    }

    /**
     * Change admin menu state
     *
     * @param bool $state
     *
     * @return array
     */
    public function actionSetMenuState($state)
    {
        \Yii::$app->session->set('bridge-menu-state', $state === 'true' ? 1 : 0);

        \Yii::$app->response->format = Response::FORMAT_JSON;

        return ['state' => $state];
    }

    /**
     * Clear cache action.
     *
     * @return string
     */
    public function actionClearCache()
    {
        if(\Yii::$app->cache->flush()) {
            Toastr::success(\Yii::t('bridge', 'Cache is cleared!'));
        }

        return \Yii::$app->response->redirect(\Yii::$app->request->referrer);
    }
}