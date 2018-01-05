<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 1/4/18
 * Time: 14:35
 */

use naffiq\bridge\gii\helpers\ColumnHelper;

class ColumnHelperTest extends \PHPUnit\Framework\TestCase
{
    public function testEndsWithTrue()
    {
        $columnName = 'thumbImage';
        $this->assertTrue(ColumnHelper::endsWith($columnName, 'image'));

        $columnName = 'thumb_image';
        $this->assertTrue(ColumnHelper::endsWith($columnName, 'image'));

        $columnName = 'thumb_image';
        $this->assertTrue(ColumnHelper::endsWith($columnName, ['image', 'avatar']));

        $columnName = 'thumb_avatar';
        $this->assertTrue(ColumnHelper::endsWith($columnName, ['image', 'avatar']));
    }

    public function testEndsWithFalse()
    {
        $columnName = 'thumbImage';
        $this->assertFalse(ColumnHelper::endsWith($columnName, 'image', true));

        $columnName = 'thumbImage';
        $this->assertFalse(ColumnHelper::endsWith($columnName, 'time'));

        $columnName = 'thumb_image';
        $this->assertFalse(ColumnHelper::endsWith($columnName, 'date'));

        $columnName = 'thumb_image';
        $this->assertFalse(ColumnHelper::endsWith($columnName, ['time', 'date']));

        $columnName = 'thumb_avatar';
        $this->assertFalse(ColumnHelper::endsWith($columnName, ['time', 'date']));
    }

    public function testBeginsWithTrue()
    {
        $columnName = 'is_active';
        $this->assertTrue(ColumnHelper::beginsWith($columnName, 'is_'));

        $columnName = 'is_active';
        $this->assertTrue(ColumnHelper::beginsWith($columnName, ['is_', 'at_']));
    }

    public function testBeginsWithFalse()
    {
        $columnName = 'is_active';
        $this->assertFalse(ColumnHelper::beginsWith($columnName, 'at_'));

        $columnName = 'is_active';
        $this->assertFalse(ColumnHelper::beginsWith($columnName, ['time_', 'date_']));
    }

    public function testPushTab()
    {
        $this->assertEquals('        ', ColumnHelper::pushTab(2));
    }

    public function testGenerateInputType()
    {
        $data = [
            'password' => ['passwordInput', true],
            'thumb_image' => ['imageUpload', true],
            'user_avatar' => ['imageUpload', true],
            'created_at' => ['dateTimePicker', true],
            'update_time' => ['dateTimePicker', true],
            'expire_date' => ['datePicker', true],
            'is_active' => ['switchInput', true],
            'main_image' => ['textInput', false],
            'anyOtherField' => ['textInput', true],
        ];

        foreach ($data as $columnName => $params) {
            $this->assertEquals($params[0], ColumnHelper::generateInputType($columnName, $params[1]));
        }
    }

    public function testHasMaxLength()
    {
        $data = [
            'password' => [true, true],
            'thumb_image' => [false, true],
            'user_avatar' => [false, true],
            'created_at' => [false, true],
            'update_time' => [false, true],
            'expire_date' => [false, true],
            'is_active' => [false, true],
            'main_image' => [true, false],
            'anyOtherField' => [true, true],
        ];

        foreach ($data as $columnName => $params) {
            $this->assertEquals($params[0], ColumnHelper::hasMaxLength($columnName, $params[1]));
        }
    }
}