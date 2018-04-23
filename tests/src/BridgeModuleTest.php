<?php

use naffiq\bridge\BridgeComponent;
use naffiq\bridge\BridgeModule;
use yii\web\Application;

/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 10.11.2017
 * Time: 15:41
 */

class BridgeModuleTest extends \PHPUnit\Framework\TestCase
{

    public function testWebAppBootstrap()
    {
        $this->assertInstanceOf(Application::class, \Yii::$app);
        $this->assertTrue(\Yii::$app->hasModule('user'));
        $this->assertNotNull(\Yii::getAlias('@bridge'));
        $this->assertNotNull(\Yii::getAlias('@bridge-assets'));
        $this->assertInstanceOf(BridgeComponent::class, \Yii::$app->bridge);

    }

    public function checkAdminBeforeAction()
    {
        $this->assertFalse(\Yii::$app->bridge->isAdmin);
        \Yii::$app->getModule('admin')->trigger(BridgeModule::EVENT_BEFORE_ACTION);
        $this->assertTrue(\Yii::$app->bridge->isAdmin);
    }
}