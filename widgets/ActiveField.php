<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 8/2/2017
 * Time: 11:57 PM
 */

namespace naffiq\bridge\widgets;
use kartik\select2\Select2;
use kartik\widgets\DatePicker;
use kartik\widgets\DateTimePicker;
use kartik\widgets\FileInput;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class ActiveField
 *
 * Has some shortcuts
 *
 * @package naffiq\bridge\widgets
 */
class ActiveField extends \yii\widgets\ActiveField
{
    /**
     * @param array $options
     * @return $this
     */
    public function richTextArea($options = [])
    {
        return $this->widget(TinyMce::className(), $options);
    }

    /**
     * @param array $options
     * @return $this
     */
    public function imageUpload($options = [])
    {
        return $this->widget(FileInput::className(), ArrayHelper::merge([
            'pluginOptions' => [
                'showUpload' => false,
                'showRemove' => false,
            ]
        ], $options));
    }

    /**
     * Wrapper for kartik-v/yii2-widgets-select
     *
     * @param $data
     * @param array $options
     * @return $this
     */
    public function select2($data, $options = [])
    {
        return $this->widget(Select2::className(), ArrayHelper::merge([
            'data' => $data
        ], $options));
    }

    /**
     * Renders dropdown (select2) with any records from database, that found via `$arClass` ActiveRecord.
     * If `$arClass` implements getDropDownData() static method, then select will be filled with returned value.
     *
     * @param $arClass
     * @param string $value
     * @param string $label
     * @param array $selectOptions
     * @return ActiveField
     */
    public function relationalDropDown($arClass, $value = 'id', $label = 'title', $selectOptions = [])
    {
        if (method_exists($arClass, 'getDropDownData')) {
            $dropDownData = $arClass::getDropDownData();
        } else {
            $dropDownData = $arClass::find()->all();
        }

        return $this->select2(ArrayHelper::map($dropDownData, $value, $label), $selectOptions);
    }

    /**
     * @param array $options
     * @return $this
     */
    public function datePicker($options = [])
    {
        return $this->widget(DatePicker::className(), $options);
    }

    public function dateTimePicker($options = [])
    {
        return $this->widget(DateTimePicker::className(), $options);
    }


}