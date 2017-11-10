<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 6/7/2017
 * Time: 10:38 PM
 */

namespace naffiq\bridge\assets;


use naffiq\bridge\BridgeModule;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\AssetBundle;

class AdminAsset extends AssetBundle
{
    public $sourcePath = '@bridge-assets';

    public $css = [
        'admin.css',
        '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
        '//maxcdn.bootstrapcdn.com/bootswatch/latest/flatly/bootstrap.min.css',
        '//cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/0.8.1/css/perfect-scrollbar.min.css'
    ];

    public $js = [
        '//cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/0.8.1/js/perfect-scrollbar.jquery.min.js',
        'admin.main.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $adminModule = \Yii::$app->getModule('admin');

        if (!$adminModule instanceof BridgeModule) {
            throw new InvalidConfigException('You have to set `admin` module key in app config to BridgeModule::class');
        }

        $this->js = ArrayHelper::merge($this->js, $adminModule->extraJs);
        $this->css = ArrayHelper::merge($this->css, $adminModule->extraCss);

        if (!is_array($adminModule->extraAssets)) {
            throw new InvalidConfigException('Invalid `admin` module config for `extraAssets` — it should be array with AssetBundle classes');
        }
        foreach ($adminModule->extraAssets as $asset) {
            if (!$asset instanceof AssetBundle) {
                throw new InvalidConfigException('Invalid `admin` module config for `extraAssets` — it should be array with AssetBundle classes');
            }

            $this->depends[] = $asset;
        }
    }
}