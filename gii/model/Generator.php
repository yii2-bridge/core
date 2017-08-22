<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 8/7/17
 * Time: 18:09
 */

namespace naffiq\bridge\gii\model;

use mongosoft\file\UploadImageBehavior;
use naffiq\bridge\gii\helpers\ArrayString;
use yii\base\NotSupportedException;
use yii\db\ColumnSchema;
use yii\db\Schema;
use yii\db\TableSchema;
use yii\gii\CodeFile;
use Yii;
use yii\helpers\ArrayHelper;

class Generator extends \yii\gii\generators\model\Generator
{
    /**
     * @var boolean generates behaviors for model if true
     */
    public $generateBehaviors = true;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['generateBehaviors', 'safe']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'generateBehaviors' => 'Generate behaviors based on column names'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return ArrayHelper::merge(parent::hints(), [
            'generateBehaviors' => 'If column name has <code>image</code> at the end, <code>ImageUploadBehavior</code> will be added'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];
        $relations = $this->generateRelations();
        $db = $this->getDbConnection();
        foreach ($this->getTableNames() as $tableName) {
            // model :
            $modelClassName = $this->generateClassName($tableName);
            $queryClassName = ($this->generateQuery) ? $this->generateQueryClassName($modelClassName) : false;
            $tableSchema = $db->getTableSchema($tableName);

            // behaviors:
            $behaviors = $this->generateBehaviors ? $this->generateBehaviors($tableSchema) : [];

            $params = [
                'tableName' => $tableName,
                'className' => $modelClassName,
                'queryClassName' => $queryClassName,
                'tableSchema' => $tableSchema,
                'labels' => $this->generateLabels($tableSchema),
                'rules' => $this->generateRules($tableSchema),
                'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
                'behaviors' => $behaviors
            ];
            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $modelClassName . '.php',
                $this->render('model.php', $params)
            );

            // query :
            if ($queryClassName) {
                $params['className'] = $queryClassName;
                $params['modelClassName'] = $modelClassName;
                $files[] = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $this->queryNs)) . '/' . $queryClassName . '.php',
                    $this->render('query.php', $params)
                );
            }
        }

        return $files;
    }

    /**
     * Generates behaviors for table columns based on its' name
     *
     * @param TableSchema $table
     * @return array
     */
    protected function generateBehaviors($table)
    {
        $behaviors = [];
        foreach ($table->columns as $column) {
            if ($this->endsWith($column, 'image')) {
                $behaviors[$column->name . 'Upload'] = [
                    'class' => UploadImageBehavior::className(),
                    'attribute' => $column->name,
                    'path' => '@webroot/media/'.$table->name.'/{id}',
                    'url' => '@web/media/'.$table->name.'/{id}',
                    'scenarios' => new ArrayString(['create', 'update'], true),
                ];
            }

            if ($this->endsWith($column, 'file')) {
                $behaviors[$column->name . 'File'] = [

                ];
            }
        }

        return $behaviors;
    }

    protected function generateBehaviorMethods($behaviors)
    {

    }

    /**
     * @param ColumnSchema $column
     * @param string|array $endString
     * @return bool
     */
    private function endsWith($column, $endString)
    {
        $columnName = strtolower($column->name);
        if (is_array($endString)) {
            foreach ($endString as $string) {
                if ($this->endsWith($column, $string)) {
                    return true;
                }
            }
            return false;
        } else {
            return strpos($columnName, $endString) === strlen($columnName) - strlen($endString);
        }
    }

    /**
     * Generates validation rules for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated validation rules
     */
    public function generateRules($table)
    {
        $types = [];
        $lengths = [];
        foreach ($table->columns as $column) {
            if ($column->autoIncrement) {
                continue;
            }
            if (!$column->allowNull && $column->defaultValue === null) {
                $types['required'][] = $column->name;
            }

            if ($this->generateBehaviors && $this->endsWith($column, 'image')) {
                $type['image'][] = $column->name;
            } else {
                switch ($column->type) {
                    case Schema::TYPE_SMALLINT:
                    case Schema::TYPE_INTEGER:
                    case Schema::TYPE_BIGINT:
                        $types['integer'][] = $column->name;
                        break;
                    case Schema::TYPE_BOOLEAN:
                        $types['boolean'][] = $column->name;
                        break;
                    case Schema::TYPE_FLOAT:
                    case 'double': // Schema::TYPE_DOUBLE, which is available since Yii 2.0.3
                    case Schema::TYPE_DECIMAL:
                    case Schema::TYPE_MONEY:
                        $types['number'][] = $column->name;
                        break;
                    case Schema::TYPE_DATE:
                    case Schema::TYPE_TIME:
                    case Schema::TYPE_DATETIME:
                    case Schema::TYPE_TIMESTAMP:
                        $types['safe'][] = $column->name;
                        break;
                    default: // strings
                        if ($column->size > 0) {
                            $lengths[$column->size][] = $column->name;
                        } else {
                            $types['string'][] = $column->name;
                        }
                }
            }

        }
        $rules = [];
        foreach ($types as $type => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], '$type']";
        }
        foreach ($lengths as $length => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], 'string', 'max' => $length]";
        }

        $db = $this->getDbConnection();

        // Unique indexes rules
        try {
            $uniqueIndexes = $db->getSchema()->findUniqueIndexes($table);
            foreach ($uniqueIndexes as $uniqueColumns) {
                // Avoid validating auto incremental columns
                if (!$this->isColumnAutoIncremental($table, $uniqueColumns)) {
                    $attributesCount = count($uniqueColumns);

                    if ($attributesCount === 1) {
                        $rules[] = "[['" . $uniqueColumns[0] . "'], 'unique']";
                    } elseif ($attributesCount > 1) {
                        $labels = array_intersect_key($this->generateLabels($table), array_flip($uniqueColumns));
                        $lastLabel = array_pop($labels);
                        $columnsList = implode("', '", $uniqueColumns);
                        $rules[] = "[['$columnsList'], 'unique', 'targetAttribute' => ['$columnsList'], 'message' => 'The combination of " . implode(', ', $labels) . " and $lastLabel has already been taken.']";
                    }
                }
            }
        } catch (NotSupportedException $e) {
            // doesn't support unique indexes information...do nothing
        }

        // Exist rules for foreign keys
        foreach ($table->foreignKeys as $refs) {
            $refTable = $refs[0];
            $refTableSchema = $db->getTableSchema($refTable);
            if ($refTableSchema === null) {
                // Foreign key could point to non-existing table: https://github.com/yiisoft/yii2-gii/issues/34
                continue;
            }
            $refClassName = $this->generateClassName($refTable);
            unset($refs[0]);
            $attributes = implode("', '", array_keys($refs));
            $targetAttributes = [];
            foreach ($refs as $key => $value) {
                $targetAttributes[] = "'$key' => '$value'";
            }
            $targetAttributes = implode(', ', $targetAttributes);
            $rules[] = "[['$attributes'], 'exist', 'skipOnError' => true, 'targetClass' => $refClassName::className(), 'targetAttribute' => [$targetAttributes]]";
        }

        return $rules;
    }
}