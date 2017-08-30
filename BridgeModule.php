<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 6/5/2017
 * Time: 1:19 AM
 */

namespace naffiq\bridge;

use naffiq\bridge\models\Users;
use yii\base\BootstrapInterface;
use yii\base\Module;
use yii\console\Application as ConsoleApplication;
use yii\console\controllers\MigrateController;
use yii\helpers\ArrayHelper;
use yii\web\Application as WebApplication;


/**
 * Class BridgeModule
 *
 * Main module for Bridge admin panel. Register your custom modules as submodules to this class in your config file.
 *
 * @package naffiq\bridge
 */
class BridgeModule extends Module implements BootstrapInterface
{
    /**
     * @var array Menu items shown in admin panel (except for default ones)
     */
    public $menu = [];

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        \Yii::setAlias('@bridge', \Yii::getAlias('@vendor/naffiq/yii2-bridge'));
        \Yii::setAlias('@bridge-assets', \Yii::getAlias('@vendor/naffiq/yii2-bridge/assets/dist/'));
        \Yii::setAlias('@bridge-migrations', \Yii::getAlias('@vendor/naffiq/yii2-bridge/migrations/'));

        if ($app instanceof WebApplication) {
            $app->getUrlManager()->addRules([
                ['class' => 'yii\web\UrlRule', 'pattern' => $this->id, 'route' => $this->id . '/default/index'],
                ['class' => 'yii\web\UrlRule', 'pattern' => $this->id . '/<id:\w+>', 'route' => $this->id . '/default/view'],
                ['class' => 'yii\web\UrlRule', 'pattern' => $this->id . '/<controller:[\w\-]+>/<action:[\w\-]+>', 'route' => $this->id . '/<controller>/<action>'],
                ['class' => 'yii\web\UrlRule', 'pattern' => $this->id . '/<module:[\w\-]+>/<controller:[\w\-]+>/<action:[\w\-]+>', 'route' => $this->id . '/<module>/<controller>/<action>'],
            ], false);

            $app->user->loginUrl = [$this->id . '/default/login'];
            $app->user->identityClass = Users::className();

            $app->i18n->translations['yii2tech-admin'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@yii2tech/admin/messages',
            ];
        } elseif ($app instanceof ConsoleApplication) {

        }

        // Registering yii2-usuario module
        if (!$app->getModule('user') || !($app->getModule('user') instanceof \Da\User\Module)) {
            $app->setModule('user', ['class' => 'Da\User\Module']);
        }

        // Registering custom Gii generators
        if (!empty($app->getModule('gii'))) {
            $app->getModule('gii')->generators['adminCrud'] = [
                'class' => 'naffiq\bridge\gii\crud\Generator'
            ];
            $app->getModule('gii')->generators['model'] = [
                'class' => 'naffiq\bridge\gii\model\Generator'
            ];
        }
    }

    /**
     * @inheritdoc
     *
     * Adds menu items loaded from config to View params
     */
    public function init()
    {
        parent::init();

        if (empty(\Yii::$app->view->params['admin-menu'])) {
            \Yii::$app->view->params['admin-menu'] = $this->menu;
        } else {
            \Yii::$app->view->params['admin-menu'] = ArrayHelper::merge(
                \Yii::$app->view->params['admin-menu'], $this->menu
            );
        }
    }


}