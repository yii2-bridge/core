<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 6/7/2017
 * Time: 10:38 PM
 */

namespace naffiq\bridge\assets;


use mihaildev\elfinder\Assets;
use yii\web\AssetBundle;

class ElFinderTheme extends AssetBundle
{
    public $sourcePath = '@bridge-assets/elfinder';

    public $css = [
        'theme' => 'css/theme.css',
    ];

    public $depends = [
        Assets::class
    ];
}