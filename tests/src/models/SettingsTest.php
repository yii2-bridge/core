<?php

use Bridge\Core\Models\Settings;
use Bridge\Core\Models\SettingsGroup;

/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 3/27/18
 * Time: 21:27
 */

class SettingsTest extends \PHPUnit\Framework\TestCase
{

    public function testGroupBasic()
    {
        $contactsSettingsGroup = Settings::group('contacts');

        $this->assertInstanceOf(SettingsGroup::class, $contactsSettingsGroup);
        $this->assertFalse($contactsSettingsGroup->isNewRecord);
    }
}