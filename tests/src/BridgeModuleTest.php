<?php
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
        $this->assertInstanceOf(\yii\web\Application::className(), \Yii::$app);
        $this->assertTrue(\Yii::$app->hasModule('user'));
        $this->assertNotNull(\Yii::getAlias('@bridge'));
        $this->assertNotNull(\Yii::getAlias('@bridge-assets'));
    }
}