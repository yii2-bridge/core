<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 8/7/17
 * Time: 17:18
 */

namespace naffiq\bridge\gii\crud;


use naffiq\bridge\gii\helpers\ColumnHelper;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;

class Generator extends \yii2tech\admin\gii\crud\Generator
{
    /**
     * @inheritdoc
     */
    public $baseControllerClass = 'naffiq\bridge\controllers\BaseAdminController';

    /**
     * @var string Generates controller with create scenario
     */
    public $createScenario = 'create';

    /**
     * @var string Generates controller with update scenario
     */
    public $updateScenario = 'update';

    /**
     * @inheritdoc
     */
    public function generateActiveField($attribute)
    {
        $tableSchema = $this->getTableSchema();

        $column = $tableSchema->columns[$attribute];

        $cancelMaxLength = false;
        if ($column->phpType === 'boolean') {
            return "\$form->field(\$model, '$attribute')->switchInput()";
        } elseif ($column->type === 'text') {
            return "\$form->field(\$model, '$attribute')->richTextArea(['options' => ['rows' => 6]])";
        }

        if (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name)) {
            $input = 'passwordInput';
        } elseif (ColumnHelper::endsWith($column, 'image')) {
            $input = 'imageUpload';
            $cancelMaxLength = true;
        } elseif (ColumnHelper::endsWith($column, 'file')) {
            $input = 'fileUpload';
            $cancelMaxLength = true;
        } elseif (ColumnHelper::endsWith($column, ['_at', 'time'])) {
            $input = 'dateTimePicker';
            $cancelMaxLength = true;
        } elseif (ColumnHelper::endsWith($column, ['date'])) {
            $input = 'datePicker';
            $cancelMaxLength = true;
        } else {
            $input = 'textInput';
        }

        if (is_array($column->enumValues) && count($column->enumValues) > 0) {
            $dropDownOptions = [];
            foreach ($column->enumValues as $enumValue) {
                $dropDownOptions[$enumValue] = Inflector::humanize($enumValue);
            }
            return "\$form->field(\$model, '$attribute')->dropDownList("
                . preg_replace("/\n\s*/", ' ', VarDumper::export($dropDownOptions)) . ", ['prompt' => ''])";
        } elseif ($column->phpType !== 'string' || $column->size === null || $cancelMaxLength) {
            return "\$form->field(\$model, '$attribute')->$input()";
        }

        return "\$form->field(\$model, '$attribute')->$input(['maxlength' => true])";
    }
}