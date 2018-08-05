<?php

use Bridge\Core\Models\Settings;
use yii\base\InvalidParamException;

/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 3/27/18
 * Time: 21:27
 */

class SettingsGroupTest extends \PHPUnit\Framework\TestCase
{
    /**
     *
     */
    public function testGetOrCreate()
    {
        $email = Settings::group('contacts')->getOrCreate('email', [
            'value' => 'naffiq@gmail.com'
        ]);

        $this->assertEquals(Settings::group('contacts')->id, $email->group_id);
    }

    public function testGetFail()
    {
        $this->expectException(InvalidParamException::class);
        Settings::group('contacts')->get('somesettingthatdoesntevenexist');
    }
}