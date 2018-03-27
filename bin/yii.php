<?php
/**
 * Console yii app. Used for development purposes (to create migrations and etc).
 *
 * @author naffiq <naffiq@gmail.com>
 * @since v0.9.0
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

if (file_exists(__DIR__. '/../.env')) {
    $dotenv = new Dotenv\Dotenv(__DIR__ . '/../');
    $dotenv->load();
}

(new \yii\console\Application([
    'id' => 'bridge-console',
    'basePath' => __DIR__ . '/../src/',
    'bootstrap' => ['admin'],
    'aliases' => [
        '@Da/User' => '@vendor/2amigos/yii2-usuario/src/User',
        '@Zelenin/yii/modules/I18n' => '@vendor/zelenin/yii2-i18n-module',
    ],
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=' . (getenv('DB_NAME') ?: 'yii2_bridge_test'),
            'username' => getenv('DB_USER') ?: 'root',
            'password' => getenv('DB_PASS') ?: '',
            'charset' => 'utf8',
        ],
        'authManager' => [
            'class' => \Da\User\Component\AuthDbManagerComponent::class
        ],
        'i18n' => [
            'class' => Zelenin\yii\modules\I18n\components\I18N::class
        ]
    ],
    'modules' => [
        'admin' => [
            'class' => \naffiq\bridge\BridgeModule::class
        ]
    ]
]))->run();
