<?php

return [
    'id' => 'yii2-bridge-test-app',
    'basePath' => __DIR__,
    'bootstrap' => ['admin'],
    'aliases' => [
        '@Da/User' => '@vendor/2amigos/yii2-usuario/src/User',
    ],
    'components' => [
        'user' => [
            'identityClass' => \Da\User\Model\User::class
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=yii2-bridge-test',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ]
    ],
    'modules' => [
        'admin' => [
            'class' => \naffiq\bridge\BridgeModule::class,
        ]
    ]
];