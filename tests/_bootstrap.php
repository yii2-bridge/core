<?php

if (file_exists(__DIR__. '/../.env')) {
    $dotenv = new Dotenv\Dotenv(__DIR__ . '/../');
    $dotenv->load();
}

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

new \yii\web\Application(require __DIR__  . '/config/test.php');