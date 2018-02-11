<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 8/7/17
 * Time: 18:09
 */

namespace naffiq\bridge\gii\model;

use mongosoft\file\UploadBehavior;
use mongosoft\file\UploadImageBehavior;
use naffiq\bridge\gii\helpers\ArrayString;
use naffiq\bridge\gii\helpers\ColumnHelper;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use yii\base\NotSupportedException;
use yii\db\ColumnSchema;
use yii\db\Schema;
use yii\db\TableSchema;
use yii\gii\CodeFile;
use Yii;
use yii\helpers\ArrayHelper;
use yii2tech\ar\position\PositionBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

class Generator extends \yii\gii\generators\model\Generator
{
    /**
     * @var boolean generates behaviors for model if true
     */
    public $generateBehaviors = true;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Bridge Model Generator';
    }

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
            'generateBehaviors' => '<h5>List of behaviors to be generated:</h5>
<ul>
    <li>If column name has <code>image</code> at the end, <code>ImageUploadBehavior</code> will be added</li>
    <li><code>PositionBehavior</code> added to model, if there is <code>position</code> column in table</li>
    <li><code>SoftDeleteBehavior</code> for <code>isDeleted</code> attribute</li>
</ul>'
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
                'behaviors' => $behaviors,
                'behaviorMethods' => $this->generateBehaviorMethods($behaviors)
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
            if (ColumnHelper::endsWith($column->name, ['image', 'avatar'])) {
                $behaviors[$column->name . 'Upload'] = [
                    'class' => UploadImageBehavior::className(),
                    'attribute' => $column->name,
                    'path' => '@webroot/media/'.$table->name.'/{id}',
                    'url' => '@web/media/'.$table->name.'/{id}',
                    'scenarios' => new ArrayString(['create', 'update'], true),
                    'thumbs' => new ArrayString([
                        'thumb' => new ArrayString(['width' => 200, 'height' => 200, 'quality' => 90]),
                        'preview' => new ArrayString(['width' => 50, 'height' => 50, 'quality' => 90])
                    ])
                ];
            }

            if (ColumnHelper::endsWith($column->name, 'file')) {
                $behaviors[$column->name . 'File'] = [
                    'class' => UploadBehavior::className(),
                    'attribute' => $column->name,
                    'path' => '@webroot/media/'.$table->name.'/{id}',
                    'url' => '@web/media/'.$table->name.'/{id}',
                    'scenarios' => new ArrayString(['create', 'update'], true),
                ];
            }

            if ($column->name === 'position') {
                $behaviors[$column->name . 'Sort'] = [
                    'class' => PositionBehavior::className(),
                    'positionAttribute' => $column->name
                ];
            }

            if ($column->name === 'isDeleted') {
                $behaviors['softDeleteBehavior'] = [
                    'class' => SoftDeleteBehavior::className(),
                    'softDeleteAttributeValues' => new ArrayString([
                        $column->name => true
                    ])
                ];
            }
        }

        return $behaviors;
    }

    /**
     * Generates phpDoc for given behaviors (result of `Generator::generateBehaviors()` method)
     *
     * @param $behaviors
     * @return array
     */
    protected function generateBehaviorMethods($behaviors)
    {
        $methods = [];

        foreach ($behaviors as $behavior) {
            if (empty($behavior['class'])) {
                continue;
            }
            $reflection = new \ReflectionClass($behavior['class']);
            $baseClassReflection = new \ReflectionClass($this->baseClass);

            foreach ($reflection->getMethods() as $method) {
                if (!$method->isPublic()) {
                    continue;
                }

                $methodName = $method->getName();
                if (strpos($methodName, '__') === 0) {
                    continue;
                }

                if ($baseClassReflection->hasMethod($methodName)) {
                    continue;
                }

                if (in_array($methodName, ['events', 'attach', 'detach'])) {
                    continue;
                }

                $arguments = $this->getMethodArguments($method);

                $methods[$methodName] = [
                    'returnType' => $this->getMethodReturnType($method),
                    'arguments' => $arguments,
                    'description' => $this->getMethodSummary($method)
                ];
            }
        }

        return $methods;
    }

    /**
     * Returns method's arguments as an array of strings
     *
     * @param \ReflectionMethod $method
     * @return array
     */
    protected function getMethodArguments(\ReflectionMethod $method)
    {
        $result = [];
        foreach ($method->getParameters() as $parameter) {
            $parameterType = '';
            if (method_exists($parameter, 'hasType') && $parameter->getType()) {
                $parameterType = $parameter->getType() . ' ';
            }

            $defaultValue = '';
            if ($parameter->isDefaultValueAvailable()) {
                if ($parameter->getDefaultValue() === null) {
                    $defaultValue = ' = null';
                } elseif($parameter->getDefaultValue() === false) {
                    $defaultValue = ' = false';
                } elseif ($parameter->isDefaultValueConstant()) {
                    $defaultValue = ' = ' . $parameter->getDefaultValueConstantName();
                } else {
                    $value = $parameter->getDefaultValue();

                    if (is_numeric($value)) {
                        $defaultValue = ' = ' . $value;
                    } else {
                        $defaultValue = " = '{$value}'";
                    }
                }
            }

            $result[] = $parameterType . '$'.$parameter->getName() . $defaultValue;
        }
        return $result;
    }

    /**
     * Returns method summary
     *
     * @param \ReflectionMethod $method
     * @return string
     */
    protected function getMethodSummary(\ReflectionMethod $method)
    {
        return $this->getMethodDocBlock($method)->getSummary() ?: '';
    }

    /**
     * Returns method docBlock return tag value
     *
     * @param \ReflectionMethod $method
     * @return string
     */
    protected function getMethodReturnType(\ReflectionMethod $method)
    {
        $docBlock = $this->getMethodDocBlock($method);

        if ($docBlock->hasTag('return')) {
            $tag = $docBlock->getTagsByName('return')[0];
            $trimmedTag = trim(str_replace('@return', '', $tag->render()));

            return explode(' ', $trimmedTag)[0];
        }

        return 'mixed';
    }

    /**
     * Returns DocBlock for given method to generate further attributes and stuff
     *
     * @param \ReflectionMethod $method
     * @return DocBlock
     */
    protected function getMethodDocBlock(\ReflectionMethod $method): DocBlock
    {
        $factory  = DocBlockFactory::createInstance();

        $methodComment = $method->getDocComment();
        if (strpos(strtolower($methodComment), '@inheritdoc')) {
            $parentClassMethod = $method->getDeclaringClass()->getParentClass()->getMethod($method->name);
            return $this->getMethodDocBlock($parentClassMethod);
        }

        return $factory->create($methodComment);
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
        $files = [];
        foreach ($table->columns as $column) {
            if ($column->autoIncrement) {
                continue;
            }
            if (!$column->allowNull && $column->defaultValue === null) {
                $types['required'][] = $column->name;
            }

            if ($this->generateBehaviors && ColumnHelper::endsWith($column->name, ['image', 'avatar'])) {
                $files["['gif', 'jpg', 'png', 'jpeg']"][] = $column->name;
            } elseif ($this->generateBehaviors && ColumnHelper::endsWith($column->name, 'file')) {
                $files["null"][] = $column->name;
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

        foreach ($files as $extensions => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], 'file', 'on' => ['create', 'update'], 'extensions' => $extensions]";
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