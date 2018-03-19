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
use Da\User\Model\User;
use naffiq\bridge\assets\ElFinderTheme;
use naffiq\bridge\models\Settings;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\base\Module;
use yii\console\Application as ConsoleApplication;
use yii\helpers\ArrayHelper;
use yii\web\Application as WebApplication;
use yii\web\View;
use Zelenin\yii\modules\I18n\controllers\DefaultController;


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

    public $languageSwitchAction = '\naffiq\bridge\controllers\actions\LanguageSwitchAction';

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
    public $version = 'v0.8.0';

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

    public $elFinderConfig = [];

    /**
     * @var null|callable|array an anonymous function, that recieves Application instance
     */
    public $languageInitHandler = null;

    /**
     * @var array
     */
    public $languages = [
        'en-US' => 'EN',
        'ru-RU' => 'RU',
        'kk-KZ' => 'KZ',
    ];

    public $defaultLanguage = 'en-US';

    public $showLanguageSwitcher = true;

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $this->registerTranslations($app);

        if ($app instanceof WebApplication) {
            $this->registerAliases();
            $this->registerRoutes($app);
            $this->registerElFinder();

            if ($app->user->identityClass !== User::class && !is_subclass_of($app->user->identityClass, User::class)) {
                if ($this->userClass !== User::class && !is_subclass_of($this->userClass, User::class)) {
                    throw new InvalidConfigException(
                        "Either \"\\Yii::\$app->user->identityClass\" or \"BridgeModule::userClass\" should be subclass of \"\\Da\\User\\Model\\User\"."
                        . ' Please configure your app appropriately.'
                    );
                }

                $app->user->identityClass = $this->userClass;
            }
        } elseif ($app instanceof ConsoleApplication) {
            \Yii::setAlias('@bridge-migrations', \Yii::getAlias('@vendor/naffiq/yii2-bridge/src/migrations/'));
            \Yii::setAlias('@naffiq/bridge', \Yii::getAlias('@vendor/naffiq/yii2-bridge/src/'));
        }

        $this->registerGiiGenerators($app);

        $this->registerUsuario($app);

        \Yii::$app->on(\yii\web\Application::EVENT_BEFORE_ACTION, function () {
            $this->registerGoogleAnalytics();
            $this->registerYandexMetrika();
        });
    }

    /**
     * @inheritdoc
     *
     * Redefine login URL for admin actions
     */
    public function beforeAction($action)
    {
        $app = \Yii::$app;

        if ($app instanceof \yii\web\Application) {
            \Yii::$app->user->loginUrl = $this->loginUrl ?: [$this->id . '/default/login'];
        }

        return parent::beforeAction($action);
    }

    /**
     * Register ElFinder controller in `BridgeModule::controllerMap`
     */
    private function registerElFinder()
    {
        $this->controllerMap = ArrayHelper::merge($this->controllerMap, [
            'elfinder' => ArrayHelper::merge([
                'class' => 'mihaildev\elfinder\Controller',
                'access' => $this->allowedRoles,
                'disabledCommands' => ['netmount'],
                'roots' => [
                    [
                        'baseUrl' => '@web',
                        'basePath' => '@webroot',
                        'path' => 'media/',
                        'name' => 'Global'
                    ],
                ],
                'on beforeAction' => function () {
                    ElFinderTheme::register(\Yii::$app->view);
                },
                'layout' => '@bridge/views/layouts/main.php'
            ], $this->elFinderConfig)
        ]);
    }

    /**
     * Registering app aliases
     */
    private function registerAliases()
    {
        \Yii::setAlias('@bridge', \Yii::getAlias('@vendor/naffiq/yii2-bridge/src'));
        \Yii::setAlias('@bridge-assets', \Yii::getAlias('@vendor/naffiq/yii2-bridge/src/assets/dist/'));
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
        $app->set('i18n', [
            'class' => \Zelenin\yii\modules\I18n\components\I18N::class,
            'languages' => array_keys($this->getLanguagesList())
        ]);

        $app->i18n->translations['yii2tech-admin'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@yii2tech/admin/messages',
        ];

        $app->i18n->translations['bridge'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@bridge/translations',
        ];

        if ($app instanceof \yii\web\Application) {
            $this->modules = ArrayHelper::merge($this->modules, [
                'i18n' => [
                    'class' => \Zelenin\yii\modules\I18n\Module::class,
                    'layout' => '@bridge/views/layouts/main',
                    'controllerMap' => [
                        'default' => [
                            'class' => DefaultController::class,
                            'viewPath' => '@bridge/views/i18n',
                        ]
                    ]
                ]
            ]);

            \Yii::$app->on(Application::EVENT_BEFORE_ACTION, function ($event) {
                if (is_array($this->languageInitHandler) || is_callable($this->languageInitHandler)) {
                    \Yii::$app->language = call_user_func($this->languageInitHandler);
                } else {
                    \Yii::$app->language = \Yii::$app->request->cookies->getValue('lang', $this->defaultLanguage);
                }
            });
        }


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
            $giiModule->generators['bridge-crud'] = ['class' => 'naffiq\bridge\gii\crud\Generator'];
            $giiModule->generators['bridge-model'] = ['class' => 'naffiq\bridge\gii\model\Generator'];
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
     * Composes menu for admin panel from config
     *
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
            return ArrayHelper::merge($this->getDefaultMenuItems(), empty($this->menu) ? [] : $this->menu);
        }

        return call_user_func($this->composeMenu, $user, $authManager->getRolesByUser($user->id), $authManager);
    }

    /**
     * Returns default menu items
     *
     * @return mixed
     */
    protected function getDefaultMenuItems()
    {
        return require __DIR__ . '/config/menu.php';
    }

    /**
     * Registers google analytics script
     */
    protected function registerGoogleAnalytics()
    {
        try {
            $gaKey = Settings::get('google-analytics-key');
//            \Yii::$app->view->on(View::EVENT)
            \Yii::$app->view->registerJs(<<<JS
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', '{$gaKey->value}', 'auto');
    ga('send', 'pageview');
JS
                , View::POS_HEAD);
        } catch (\Exception $e) {
        }
    }

    /**
     * Registers yandex metrika script
     */
    protected function registerYandexMetrika()
    {
        try {
            $ymKey = Settings::get('yandex-metrika-key');
            \Yii::$app->view->on(View::EVENT_BEGIN_BODY, function () use ($ymKey) {
                echo <<<HTML
<!-- Yandex.Metrika counter -->
<script type="text/javascript">(function (d, w, c) { (w[c] = w[c] || []).push(function() 
{ try { w.yaCounter28278981 = new Ya.Metrika({id:'{$ymKey->value}', webvisor:true, clickmap:true, trackLinks:true, accurateTrackBounce:true, trackHash:true}); }
catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () 
{ n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = (d.location.protocol == "https:" ? "https:" : "http:") + 
"//mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })
(document, window, "yandex_metrika_callbacks");</script>
<noscript><div><img src="//mc.yandex.ru/watch/{$ymKey->value}" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
HTML;
            });
        } catch (\Exception $e) {
        }
    }

    public function getLanguagesList()
    {
        if ((is_array($this->languages) && !ArrayHelper::isAssociative($this->languages)) || is_callable($this->languages)) {
            return call_user_func($this->languages);
        }

        return $this->languages;
    }
}
