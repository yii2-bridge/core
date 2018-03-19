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
use kartik\widgets\SwitchInput;
use mongosoft\file\UploadBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use yii\web\Application;
use yii\web\View;

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
     * Registers default width/height for image dialog.
     *
     * @var $width string
     * @var $height string
     */
    protected function registerCKEditorImageDefaults($width = '100%', $height = '')
    {
        if (!\Yii::$app instanceof Application) {
            return;
        }

        \Yii::$app->view->registerJs(<<<JS
CKEDITOR.on('dialogDefinition', function( ev ) {
    // Take the dialog window name and its definition from the event data.
    var dialogName = ev.data.name;
    var dialogDefinition = ev.data.definition;

    if ( dialogName == 'image' ) {
        var infoTab = dialogDefinition.getContents('info');
        window.infoTab = infoTab;
        
        var widthField = infoTab.get('txtWidth');
        var heightField = infoTab.get('txtHeight');
        
        widthField['default'] = '{$width}';
        heightField['default'] = '{$height}';
    }
});
JS
        , View::POS_END, 'CKEDITOR_IMAGE_DEFAULTS');
    }

    /**
     * @param array $options you can define `defaultImageWidth` and `defaultImageHeight` for CKEditor image plugin.
     * Only size one per page is currently available. Send issue or PR if interested.
     *
     * @return $this
     */
    public function richTextArea($options = [])
    {
        $width = ArrayHelper::getValue($options, 'defaultImageWidth', '100%');
        $height = ArrayHelper::getValue($options, 'defaultImageHeight', '');
        $this->registerCKEditorImageDefaults($width, $height);

        return $this->widget(CKEditor::className(), ArrayHelper::merge([
            'editorOptions' => ElFinder::ckeditorOptions(['/admin/elfinder', 'path' => 'some/sub/path'],['preset' => 'full', 'inline' => false]),
        ], $options));

    }

    public function fileUpload($options = [])
    {
        $initialPreview = [];
        if (empty($options['pluginOptions']['initialPreview']) && !empty($this->getUploadUrl())) {
            $initialPreview[] = Html::tag('div',
                Html::tag('h2', Html::tag('i', '', ['class' => 'fa fa-file-o']))
                . Html::a(basename($this->getUploadUrl()), $this->getUploadUrl(), ['target' => '_blank']),
                [
                    'class' => 'file-preview-text'
                ]
            );
        }

        return $this->widget(FileInput::className(), ArrayHelper::merge([
            'pluginOptions' => [
                'showUpload' => false,
                'showRemove' => false,
                'initialPreview' => $initialPreview
            ]
        ], $options));
    }

    /**
     * @param array $options
     * @return $this
     */
    public function imageUpload($options = [])
    {
        $initialPreview = [];
        if (!empty($this->getUploadUrl())) {
            $initialPreview[] = Html::img($this->getUploadUrl(), [
                'class' => 'file-preview-image',
                'title' => $this->model->getAttributeLabel($this->attribute),
                'alt' => $this->model->getAttributeLabel($this->attribute),
                'style' => 'max-height: 170px;'
            ]);
        }

        return $this->fileUpload(ArrayHelper::merge($options, [
            'pluginOptions' => [
                'showUpload' => false,
                'showRemove' => false,
                'initialPreview' => $initialPreview
            ]
        ]));
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

    /**
     * @param array $options
     * @return $this
     */
    public function switchInput($options = [])
    {
        return $this->widget(SwitchInput::className(), $options);
    }

    protected function getUploadUrl()
    {
        $uploadUrl = null;
        foreach ($this->model->getBehaviors() as $behavior) {
            if ($behavior instanceof UploadBehavior && $behavior->attribute == $this->attribute) {
                /**
                 * @var $behavior UploadBehavior
                 */
                $uploadUrl = call_user_func([$behavior, 'getUploadUrl'], $this->attribute);
                break;
            }
        }

        return $uploadUrl;
    }
}