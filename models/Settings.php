<?php

namespace naffiq\bridge\models;

use mongosoft\file\UploadImageBehavior;
use naffiq\bridge\models\query\SettingsQuery;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

/**
 * This is the model class for table "settings".
 *
 * @property integer $id
 * @property string $title
 * @property string $key
 * @property string $value
 * @property integer $type
 * @property string $type_settings
 */
class Settings extends \yii\db\ActiveRecord
{
    const TYPE_STRING = 1;
    const TYPE_TEXT = 2;
    const TYPE_IMAGE = 3;

    public static $types = [
        self::TYPE_STRING => 'String',
        self::TYPE_TEXT => 'Text',
        self::TYPE_IMAGE => 'Image',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'key', 'type'], 'required'],
            [['type_settings'], 'string'],
            ['value', 'safe'],
            [['type'], 'integer'],
            [['title', 'key'], 'string', 'max' => 255],

            ['value', 'image', 'extensions' => 'jpg, jpeg, gif, png', 'on' => ['create', 'update'], 'when' => function () {
                return $this->type == static::TYPE_IMAGE;
            }, 'whenClient' => new JsExpression(<<<JS
            function (attribute) {
                return $('#settings-value').attr('type') == 'file';
            }
JS
)],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'key' => 'Key',
            'value' => 'Value',
            'type' => 'Type',
            'type_settings' => 'Type Settings',
        ];
    }

    /**
     * @inheritdoc
     * @return SettingsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SettingsQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
        if ($this->type == static::TYPE_IMAGE) {
            $this->attachBehavior('uploadImage', [
                'class' => UploadImageBehavior::className(),
                'attribute' => 'value',
                'scenarios' => ['create', 'update'],
                'path' => '@webroot/media/settings/{id}',
                'url' => '@web/media/settings/{id}',
                'thumbs' => [
                    'preview' => ['width' => 50, 'height' => 50]
                ]
            ]);
        }
    }

    /**
     * @var array
     */
    protected static $prevSettings = [];

    /**
     * Looks for setting with provided `$key`
     *
     * @param string $key key to be looking for
     * @return string
     * @throws InvalidParamException when settings with key provided wasn't found
     */
    public static function get($key)
    {
        if (!empty(static::$prevSettings[$key])) {
            $model = static::$prevSettings[$key];
        } else {
            $model = static::find()->key($key)->one();
        }

        if (empty($model)) {
            throw new InvalidParamException("Setting with key '{$key}' wasn't found. Try creating it first or run getOrCreate method");
        }

        static::$prevSettings[$key] = $model;

        return $model;
    }

    /**
     * Creates setting with provided params
     *
     * @param array $params
     * @return Settings
     */
    public static function create($params)
    {
        $model = new static($params);
        $model->save();

        static::$prevSettings[$model->key] = $model;

        return $model;
    }

    /**
     * Looks for setting with provided `$key`. If not found, creates it with `$defaultParams`
     *
     * @param string $key
     * @param array $defaultParams
     * @return Settings|string
     */
    public static function getOrCreate($key, $defaultParams = [])
    {
        try {
            return self::get($key);
        } catch (InvalidParamException $e) {
            return self::create(ArrayHelper::merge([
                'key' => $key,
                'title' => $key,
                'type' => static::TYPE_STRING,
                'value' => ''
            ], $defaultParams));
        }
    }

    /**
     * Return setting's value for casting
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->type == static::TYPE_IMAGE) {
            return $this->getUploadUrl('value');
        }
        return (string) $this->value;
    }
}
