<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 8/7/17
 * Time: 17:18
 */

namespace Bridge\Core\Gii\CRUD;


use dosamigos\grid\columns\ToggleColumn;
use Bridge\Core\Gii\Helpers\ArrayString;
use Bridge\Core\Gii\Helpers\ColumnHelper;
use naffiq\bridge\widgets\columns\ImageColumn;
use naffiq\bridge\widgets\columns\TitledImageColumn;
use naffiq\bridge\widgets\columns\TruncatedTextColumn;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Schema;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;
use yii2tech\admin\grid\PositionColumn;

/**
 * Class Generator
 *
 * Custom generator, that allows developers to generate ready to user forms
 *
 * @package Bridge\Core\Gii\CRUD
 */
class Generator extends \yii2tech\admin\gii\crud\Generator
{
    /**
     * @inheritdoc
     */
    public $baseControllerClass = 'Bridge\Core\Controllers\BaseAdminController';

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
     * @var bool
     */
    public $generatePositionColumn = true;
    /**
     * @var bool
     */
    public $generateToggleColumn = true;

    public $generateSoftDelete = true;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Bridge CRUD Generator';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [
                [
                    'generateCustomFields',
                    'generatePositionColumn',
                    'generateToggleColumn',
                    'generateSoftDelete',
                ],
                'safe'
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::rules(), [
            'generateCustomFields' => 'Generate fields with input for complex data types',
            'generatePositionColumn' => 'Generate position attribute column and actions for sorting from index page',
            'generateToggleColumn' => 'Generate switch on index page',
            'generateSoftDelete' => 'Generate soft delete actions and trash tab'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return ArrayHelper::merge(parent::hints(), [
            'generateCustomFields' => 'If column name ends with file or <code>image</code>, then <code>image</code> upload input with preview would be generated.' .
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
        if (in_array($column->name, $this->skipColumns) || $this->shouldSoftDelete($column->name)) {
            return false;
        }

        if ($column->name === 'title') {
            if ($this->tableSchema->getColumn('avatar')) {
                $this->skipColumns[] = 'avatar';
                return [
                    'class' => TitledImageColumn::class,
                    'attribute' => 'title',
                    'imageAttribute' => 'avatar'
                ];
            } elseif ($this->tableSchema->getColumn('image')) {
                $this->skipColumns[] = 'image';
                return [
                    'class' => TitledImageColumn::class,
                    'attribute' => 'title',
                    'imageAttribute' => 'image'
                ];
            }
        } elseif ($column->name === 'avatar') {
            if ($this->tableSchema->getColumn('title')) {
                $this->skipColumns[] = 'title';
                return [
                    'class' => TitledImageColumn::class,
                    'attribute' => 'title',
                    'imageAttribute' => 'avatar'
                ];
            }
        } elseif ($column->name === 'image') {
            if ($this->tableSchema->getColumn('title')) {
                $this->skipColumns[] = 'title';
                return [
                    'class' => TitledImageColumn::class,
                    'attribute' => 'title',
                    'imageAttribute' => 'image'
                ];
            }
        } elseif ($column->name === 'position' && $this->generatePositionColumn) {
            return [
                'class' => PositionColumn::class,
                'value' => 'position',
                'template' => '<div class="btn-group">{first}&nbsp;{prev}&nbsp;{next}&nbsp;{last}</div>',
                'buttonOptions' => new ArrayString(['class' => 'btn btn-info btn-xs'])
            ];
        } elseif ($this->generateToggleColumn && ColumnHelper::beginsWith($column->name, 'is_')) {
            return [
                'class' => ToggleColumn::class,
                'attribute' => $column->name,
                'onValue' => 1,
                'onLabel' => 'Active',
                'offLabel' => 'Not active',
                'contentOptions' => new ArrayString(['class' => 'text-center']),
                'afterToggle' => 'function(r, data){if(r){console.log("done", data)};}',
                'filter' => new ArrayString([
                    1 => 'Active',
                    0 => 'Not active'
                ])
            ];
        }

        if ($column->phpType === 'boolean') {
            return 'boolean';
        } elseif ($column->type === 'text') {
            return [
                'class' => TruncatedTextColumn::class,
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
                'class' => ImageColumn::class,
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

    /**
     * Checks if generator should make soft delete for attribute.
     *
     * @param string|null $attribute
     * @return bool
     */
    public function shouldSoftDelete($attribute = null)
    {
        $softDeleteAttribute = $this->getSoftDeleteAttribute();
        if ($attribute && $attribute !== $softDeleteAttribute) {
            return false;
        }

        return $softDeleteAttribute && $this->generateSoftDelete && in_array($softDeleteAttribute, $this->getColumnNames());
    }

    public function getSoftDeleteAttribute($attributes = ['is_deleted', 'isDeleted'])
    {
        foreach ($this->getColumnNames() as $name) {
            if ((is_array($attributes) && in_array($name, $attributes)) || (is_string($attributes) && $name === $attributes)) {
                return $name;
            }
        }

        return null;
    }

    /**
     * Generates search conditions
     * @return array
     */
    public function generateSearchConditions()
    {
        $columns = [];
        if (($table = $this->getTableSchema()) === false) {
            $class = $this->modelClass;
            /* @var $model \yii\base\Model */
            $model = new $class();
            foreach ($model->attributes() as $attribute) {
                $columns[$attribute] = 'unknown';
            }
        } else {
            foreach ($table->columns as $column) {
                $columns[$column->name] = $column->type;
            }
        }

        $likeConditions = [];
        $hashConditions = [];
        foreach ($columns as $column => $type) {
            if ($this->shouldSoftDelete($column)) {
                continue;
            }

            switch ($type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                case Schema::TYPE_BOOLEAN:
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                    $hashConditions[] = "'{$column}' => \$this->{$column},";
                    break;
                default:
                    $likeKeyword = $this->getClassDbDriverName() === 'pgsql' ? 'ilike' : 'like';
                    $likeConditions[] = "->andFilterWhere(['{$likeKeyword}', '{$column}', \$this->{$column}])";
                    break;
            }
        }

        $conditions = [];
        if (!empty($hashConditions)) {
            $conditions[] = "\$query->andFilterWhere([\n"
                . str_repeat(' ', 12) . implode("\n" . str_repeat(' ', 12), $hashConditions)
                . "\n" . str_repeat(' ', 8) . "]);\n";
        }
        if (!empty($likeConditions)) {
            $conditions[] = "\$query" . implode("\n" . str_repeat(' ', 12), $likeConditions) . ";\n";
        }

        if ($this->shouldSoftDelete()) {
            $conditions[] = "\$query->andWhere(['{$this->getSoftDeleteAttribute()}' => \$this->{$this->getSoftDeleteAttribute()}]);";
        }

        return $conditions;
    }
}