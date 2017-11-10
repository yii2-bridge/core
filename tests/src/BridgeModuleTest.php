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
        $app = new \yii\web\Application(require dirname(__DIR__ ) . '/config/test.php');

        $this->assertInstanceOf(\yii\web\Application::className(), $app);
        $this->assertTrue($app->hasModule('user'));
        $this->assertNotNull(\Yii::getAlias('@bridge'));
        $this->assertNotNull(\Yii::getAlias('@bridge-assets'));
    }
}