<?php

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

new \yii\web\Application(require __DIR__  . '/config/test.php');