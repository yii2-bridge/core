<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 8/7/17
 * Time: 17:18
 */

namespace naffiq\bridge\gii\crud;


use naffiq\bridge\gii\helpers\ColumnHelper;
use naffiq\bridge\widgets\columns\ImageColumn;
use naffiq\bridge\widgets\columns\TitledImageColumn;
use naffiq\bridge\widgets\columns\TruncatedTextColumn;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;

/**
 * Class Generator
 *
 * Custom generator, that allows developers to generate ready to user forms
 *
 * @package naffiq\bridge\gii\crud
 */
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
     * @var bool
     */
    public $generateCustomFields = true;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['generateCustomFields', 'safe']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::rules(), [
            'generateCustomFields' => 'Generate fields with input for complex data types'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return ArrayHelper::merge(parent::hints(), [
            'generateCustomFields' => 'If column name ends with file or <code>image</code>, then <code>image</code> upload input with preview would be generated.'.
                ' For <code>_at</code> and <code>time</code> columns date time picker would be generated. For column ending with <code>date</code> date picker would be generated'
        ]);
    }

    private $skipColumns = [];

    /**
     * Generates column format
     * @param \yii\db\ColumnSchema $column
     * @return string|array|bool
     */
    public function generateGridColumnFormat($column)
    {
        if (in_array($column->name, $this->skipColumns)) {
            return false;
        }

        if ($column->name === 'title') {
            if ($this->tableSchema->getColumn('avatar')) {
                $this->skipColumns[] = 'avatar';
                return [
                    'class' => TitledImageColumn::className(),
                    'attribute' => 'title',
                    'imageAttribute' => 'avatar'
                ];
            } elseif ($this->tableSchema->getColumn('image')) {
                $this->skipColumns[] = 'image';
                return [
                    'class' => TitledImageColumn::className(),
                    'attribute' => 'title',
                    'imageAttribute' => 'image'
                ];
            }
        } elseif ($column->name === 'avatar') {
            if ($this->tableSchema->getColumn('title')) {
                $this->skipColumns[] = 'title';
                return [
                    'class' => TitledImageColumn::className(),
                    'attribute' => 'title',
                    'imageAttribute' => 'avatar'
                ];
            }
        } elseif ($column->name === 'image') {
            if ($this->tableSchema->getColumn('title')) {
                $this->skipColumns[] = 'title';
                return [
                    'class' => TitledImageColumn::className(),
                    'attribute' => 'title',
                    'imageAttribute' => 'image'
                ];
            }
        }

        if ($column->phpType === 'boolean') {
            return 'boolean';
        } elseif ($column->type === 'text') {
            return [
                'class' => TruncatedTextColumn::className(),
                'attribute' => $column->name
            ];
        } elseif (stripos($column->name, 'time') !== false && $column->phpType === 'integer') {
            return 'datetime';
        } elseif (stripos($column->name, 'email') !== false) {
            return 'email';
        } elseif (stripos($column->name, 'url') !== false) {
            return 'url';
        } elseif (ColumnHelper::endsWith($column->name, ['image', 'avatar'])) {
            return [
                'class' => ImageColumn::className(),
                'attribute' => $column->name
            ];
        } else {
            return 'text';
        }
    }

    /**
     * @inheritdoc
     */
    public function generateActiveField($attribute)
    {
        $tableSchema = $this->getTableSchema();

        $column = $tableSchema->columns[$attribute];
        $columnName = $column->name;

        if ($column->phpType === 'boolean') {
            return "\$form->field(\$model, '$attribute')->switchInput()";
        } elseif ($column->type === 'text') {
            return "\$form->field(\$model, '$attribute')->richTextArea(['options' => ['rows' => 6]])";
        }

        $hasMaxLength = ColumnHelper::hasMaxLength($columnName, $this->generateCustomFields);
        $input = ColumnHelper::generateInputType($columnName, $this->generateCustomFields);

        if (is_array($column->enumValues) && count($column->enumValues) > 0) {
            $dropDownOptions = [];
            foreach ($column->enumValues as $enumValue) {
                $dropDownOptions[$enumValue] = Inflector::humanize($enumValue);
            }
            return "\$form->field(\$model, '$attribute')->dropDownList("
                . preg_replace("/\n\s*/", ' ', VarDumper::export($dropDownOptions)) . ", ['prompt' => ''])";
        } elseif ($column->phpType !== 'string' || $column->size === null || !$hasMaxLength) {
            return "\$form->field(\$model, '$attribute')->$input()";
        }

        return "\$form->field(\$model, '$attribute')->$input(['maxlength' => true])";
    }

    /**
     * Checks if controller model has scenario to prevent errors
     *
     * @param $scenarioName
     * @return bool
     */
    public function hasScenario($scenarioName)
    {
        /** @var Model $model */
        $model = new $this->modelClass();
        return ArrayHelper::getValue($model->scenarios(), $scenarioName, null) !== null;
    }
}