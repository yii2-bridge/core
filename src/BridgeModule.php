<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 6/5/2017
 * Time: 1:19 AM
 */

namespace naffiq\bridge;

use Da\User\Bootstrap;
use Da\User\Component\AuthDbManagerComponent;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Module;
use yii\console\Application as ConsoleApplication;
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
     * @var array Links displayed in admin for guest users
     */
    public $guestMenu = [];

    /**
     * @var string class name for main admin dashboard action
     */
    public $dashboardAction = '\naffiq\bridge\controllers\actions\DashboardAction';

    /**
     * @var array Configuration passed to yii2-usuario module
     * @see \Da\User\Module
     */
    public $userSettings = [];

    /**
     * @var string String representation of class name, that will be used as UserIdentity for project
     */
    public $userClass = '\Da\User\Model\User';

    /**
     * @var array|string
     */
    public $loginUrl = null;

    /**
     * @var array additional js files for AdminAsset
     */
    public $extraJs = [];

    /**
     * @var array additional css files for AdminAsset
     */
    public $extraCss = [];

    /**
     * @var array asset dependencies. Each element should be string representing class that extends AssetBundle
     *
     * Example:
     * ```php
     * [
     *     'class' => 'naffiq\bridge\BridgeModule',
     *     'extraAssets' => [
     *         'app\assets\AdminAppAsset'
     *     ]
     * ]
     * ```
     */
    public $extraAssets = [];

    /**
     * @var string Module version
     */
    public $version = 'v0.5.8';

    /**
     * @var string Module repository API URL, used to fetch latest version
     */
    public $repoDataUrl = 'https://api.github.com/repos/naffiq/yii2-bridge/releases/latest';

    /**
     * @var array contains roles, that allowed to access admin panel. In order to change specific controllers behavior
     * you can override property `allowedRoles` inside that controller.
     */
    public $allowedRoles = ['admin'];

    /**
     * @var null|callable Function for evaluating menu for different roles. Instance of `\yii\web\User`,
     * array of roles as `$roles` and `$authManager` are passed to evaluate required menu items.
     *
     * Example:
     * ```php
     *  'composeMenu' => function ($user, $roles, $authManager) {
     *      if (in_array('admin', $roles)) {
     *          return require __DIR__ . '/menu-admin.php';
     *      }
     *      if (in_array('editor', $roles)) {
     *          return require __DIR__ . '/menu-editor.php';
     *      }
     *      if (in_array('manager', $roles)) {
     *          return require __DIR__ . '/menu-manager.php';
     *      }
     *  }
     * ```
     */
    public $composeMenu;

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $this->registerTranslations($app);

        if ($app instanceof WebApplication) {
            $this->registerAliases();
            $this->registerRoutes($app);

            $app->user->loginUrl = $this->loginUrl ?: [$this->id . '/default/login'];
            $app->user->identityClass = $this->userClass;

        } elseif ($app instanceof ConsoleApplication) {
            \Yii::setAlias('@bridge-migrations', \Yii::getAlias('@vendor/naffiq/yii2-bridge/src/migrations/'));
        }

        $this->registerGiiGenerators($app);

        $this->registerUsuario($app);
    }

    /**
     * Registering app aliases
     */
    private function registerAliases()
    {
        \Yii::setAlias('@bridge', \Yii::getAlias('@vendor/naffiq/yii2-bridge/src'));
        \Yii::setAlias('@bridge-assets', \Yii::getAlias('@vendor/naffiq/yii2-bridge/assets/dist/src'));
    }

    /**
     * Registering `yii\web\Application` routes for navigating in admin panel
     *
     * @param WebApplication $app
     */
    protected function registerRoutes(WebApplication $app)
    {
        $app->getUrlManager()->addRules([
            ['class' => 'yii\web\UrlRule', 'pattern' => $this->id, 'route' => $this->id . '/default/index'],
            ['class' => 'yii\web\UrlRule', 'pattern' => $this->id . '/<id:\w+>', 'route' => $this->id . '/default/view'],
            ['class' => 'yii\web\UrlRule', 'pattern' => $this->id . '/<controller:[\w\-]+>/<action:[\w\-]+>', 'route' => $this->id . '/<controller>/<action>'],
            ['class' => 'yii\web\UrlRule', 'pattern' => $this->id . '/<module:[\w\-]+>/<controller:[\w\-]+>/<action:[\w\-]+>', 'route' => $this->id . '/<module>/<controller>/<action>'],
        ], false);
    }

    /**
     * Reginstering yii2tech-admin and bridge translations
     *
     * @param Application $app
     */
    private function registerTranslations(Application $app)
    {
        $app->i18n->translations['yii2tech-admin'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@yii2tech/admin/messages',
        ];

        $app->i18n->translations['bridge'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@bridge/translations',
        ];
    }

    /**
     * Configuring `\Da\User\Component\AuthDbManagerComponent`, `\Da\User\Module` and bootstrapping yii2-usuario
     * package
     *
     * @param Application $app
     */
    private function registerUsuario(Application $app)
    {
        // Registering yii2-usuario module
        if (!$app->hasModule('user') || !($app->getModule('user') instanceof \Da\User\Module)) {
            $moduleConfig = ArrayHelper::merge([
                'class' => \Da\User\Module::class,
                'mailParams' => [
                    'fromEmail' => 'noreply@bridge.dev',
                    'welcomeMailSubject' => \Yii::t('bridge', 'Welcome to {0}', [$app->name]),
                ],
                'administratorPermissionName' => 'admin',
                'layout' => '@bridge/views/layouts/main',
                'enableRegistration' => false
            ], $this->userSettings);

            $app->setModule('user', $moduleConfig);
        }

        // AuthManager config for yii2-usuario
        $authManager = $app->get('authManager', false);
        if (empty($authManager === null) || !($authManager instanceof AuthDbManagerComponent)) {
            $app->set('authManager', ['class' => AuthDbManagerComponent::class]);
        }

        // Bootstrapping usuario module
        $usuarioBootstrap = new Bootstrap();
        $usuarioBootstrap->bootstrap($app);

    }

    /**
     * Registering custom Gii generators
     *
     * @param Application $app
     */
    private function registerGiiGenerators(Application $app)
    {
        /** @var \yii\gii\Module $giiModule */
        $giiModule = $app->getModule('gii');
        if (!empty($giiModule)) {
            $giiModule->generators['adminCrud'] = ['class' => 'naffiq\bridge\gii\crud\Generator'];
            $giiModule->generators['model'] = ['class' => 'naffiq\bridge\gii\model\Generator'];
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


    /**
     * @return array|mixed
     */
    public function getMenuItems()
    {
        $user = \Yii::$app->user;

        if ($user->isGuest) {
            return $this->guestMenu;
        }

        $authManager = \Yii::$app->authManager;

        if ($this->composeMenu === null) {
            return ArrayHelper::merge([
                [
                    'title' => \Yii::t('bridge', 'Profile'),
                    'url' => ['/user/profile', 'id' => $user->id],
                    'active' => ['module' => 'user', 'controller' => 'admin', 'action' => 'update'],
                    'icon' => 'user'
                ],
                [
                    'title' => \Yii::t('bridge', 'Dashboard'),
                    'url' => ['/admin/default/index'],
                    'active' => ['module' => 'admin', 'controller' => 'default'],
                    'icon' => 'grav',
                ],
                [
                    'title' => \Yii::t('bridge', 'Settings'),
                    'url' => ['/admin/settings/index'],
                    'active' => ['module' => 'admin', 'controller' => 'settings'],
                    'icon' => 'gear',
                    'isVisible' => ['admin']
                ],
                [
                    'title' => \Yii::t('bridge', 'Users'),
                    'url' => ['/user/admin/index'],
                    'active' => ['module' => 'user'],
                    'icon' => 'users',
                    'isVisible' => ['admin']
                ]
            ], empty($this->menu) ? [] : $this->menu);
        }

        return call_user_func($this->composeMenu, $user, $authManager->getRolesByUser($user->id), $authManager);
    }
}
