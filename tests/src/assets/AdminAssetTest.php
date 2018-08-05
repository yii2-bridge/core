<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 10.11.2017
 * Time: 18:27
 */

class AdminAssetTest extends \PHPUnit\Framework\TestCase
{
    public function testShouldFailIfNoModulePresent()
    {
        $this->expectExceptionMessage('You have to set `admin` module key in app config to BridgeModule::class');
        \Yii::$app->setModule('admin', null);

        new \Bridge\Core\Assets\AdminAsset();
    }

    public function testAddExtraJsAndCss()
    {
        \Yii::$app->setModule('admin', new \Bridge\Core\BridgeModule('admin', null, [
            'extraCss' => ['test_extra_css'],
            'extraJs' => ['test_extra_js']
        ]));
        $adminAsset = new \Bridge\Core\Assets\AdminAsset();

        $this->assertTrue(in_array('test_extra_css', $adminAsset->css));
        $this->assertTrue(in_array('test_extra_js', $adminAsset->js));
    }

    public function testShouldNotAddExtraAssetIfNotClass()
    {
        \Yii::$app->setModule('admin', new \Bridge\Core\BridgeModule('admin', null, [
            'extraAssets' => false
        ]));

        $this->expectExceptionMessage('Invalid `admin` module config for `extraAssets` — it should be array with AssetBundle classes');
        new \Bridge\Core\Assets\AdminAsset();
    }

    public function testShouldNotAddExtraAssetIfNotAssetBundle()
    {
        \Yii::$app->setModule('admin', new \Bridge\Core\BridgeModule('admin', null, [
            'extraAssets' => new \Bridge\Core\BridgeModule('admin')
        ]));

        $this->expectExceptionMessage('Invalid `admin` module config for `extraAssets` — it should be array with AssetBundle classes');
        new \Bridge\Core\Assets\AdminAsset();
    }

    public function testShouldAddExtraAsset()
    {
        \Yii::$app->setModule('admin', new \Bridge\Core\BridgeModule('admin', null, [
            'extraAssets' => [new \yii\web\AssetBundle()]
        ]));

        $this->assertNotNull(new \Bridge\Core\Assets\AdminAsset());
    }
}