<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 6/7/2017
 * Time: 10:38 PM
 */

namespace naffiq\bridge\assets;


use yii\web\AssetBundle;

class AdminAsset extends AssetBundle
{
    public $sourcePath = '@vendor/naffiq/yii2-bridge/assets/dist';

    public $css = [
        'css/admin.css',
        '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
        '//bootswatch.com/flatly/bootstrap.min.css',
    ];

    public $js = [
        'js/admin.main.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}