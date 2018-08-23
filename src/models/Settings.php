<?php

namespace Bridge\Core\Models;

use Bridge\Core\Behaviors\BridgeUploadImageBehavior;
use Bridge\Core\Assets\AdminAsset;
use Bridge\Core\Models\Query\SettingsQuery;
use Yii;
use yii\base\InvalidCallException;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "settings".
 *
 * @property integer $id
 * @property string $title
 * @property string $key
 * @property string $value
 * @property integer $type
 * @property integer $group_id
 * @property integer $position
 * @property string $type_settings
 */
class Settings extends \yii\db\ActiveRecord
{
    const TYPE_STRING = 1;
    const TYPE_TEXT = 2;
    const TYPE_IMAGE = 3;
    const TYPE_SWITCH = 4;
    const TYPE_MAP = 5;

    /**
     * Returns list of available types
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_STRING => Yii::t('bridge', 'String'),
            self::TYPE_TEXT => Yii::t('bridge', 'Text'),
            self::TYPE_IMAGE => Yii::t('bridge', 'Image'),
            self::TYPE_SWITCH => Yii::t('bridge', 'Checkbox'),
            self::TYPE_MAP => Yii::t('bridge', 'Map'),
        ];
    }

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
            [['type', 'group_id'], 'integer'],
            [['title', 'key'], 'string', 'max' => 255],

            ['value', 'image', 'extensions' => 'jpg, jpeg, gif, png', 'on' => ['create', 'update'], 'when' => function () {
                return $this->type == static::TYPE_IMAGE;
            }, 'whenClient' => new JsExpression(<<<JS
            function (attribute) {
                return $('#settings-value').attr('type') == 'file';
            }
JS
)],
            [['key'], 'unique', 'targetAttribute' => ['key']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => Yii::t('bridge', 'Title'),
            'key' => Yii::t('bridge', 'Key'),
            'value' => Yii::t('bridge', 'Value'),
            'type' => Yii::t('bridge', 'Type'),
            'type_settings' => Yii::t('bridge', 'Type Settings'),
            'group_id' => Yii::t('bridge', 'Settings group'),
            'position' => Yii::t('bridge', 'Position')
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'position' => [
                'class' => PositionBehavior::class,
                'groupAttributes' => ['group_id']
            ]
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
                'class' => BridgeUploadImageBehavior::class,
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
    public static $prevSettings = [];

    /**
     * Looks for setting with provided `$key`
     *
     * @deprecated since v0.9.0. It's will be required to create settings from SettingsGroup. Will be removed in v1.0.0.
     *
     * @param string $key key to be looking for
     * @return Settings
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
     * @deprecated since v0.9.0. It's will be required to create settings from SettingsGroup. Will be removed in v1.0.0.
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
     * @deprecated since v0.9.0. It's will be required to create settings from SettingsGroup.
     *
     * In order to migrate you can set `group_id` key in `$defaultParams` array. That will forcefully update
     * settings `group_id` even if it already exists.
     *
     * @param string $key
     * @param array $defaultParams
     * @return Settings|string
     */
    public static function getOrCreate($key, $defaultParams = [])
    {
        try {
            $settings = self::get($key);

            // Code
            if ($settings && empty($settings->group_id) && isset($defaultParams['group_id'])) {
                $settings->group_id = $defaultParams['group_id'];
                $settings->save(false, ['group_id']);
            }

            return $settings;
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
            if ($this->value) {
                return $this->getUploadUrl('value');
            }

            $bundle = \Yii::$app->assetManager->getBundle(AdminAsset::class);
            return \Yii::$app->assetManager->getAssetUrl($bundle, 'avatar@2x.jpg');
        }
        return (string) $this->value;
    }

    /**
     * @param string    $groupKey
     * Key to obtain the group
     * @param array     $defaults
     * Array, that contains default values, if the group doesn't exist. Pass any value (`null` preffered),
     * if `$create` is false. For available keys @see SettingsGroup::rules()
     *
     * @return SettingsGroup
     */
    public static function group($groupKey, $defaults = [])
    {
        $group = SettingsGroup::find()->where(['key' => $groupKey])->one();

        if (!$group) {
            $group = new SettingsGroup(ArrayHelper::merge([
                'key' => $groupKey,
                'title' => $groupKey
            ], $defaults));
            $group->save();
        }

        return $group;
    }

    /**
     * Short for miscellaneous. Uses or creates miscellaneous group with pre-defined config.
     *
     * @return SettingsGroup
     */
    public static function misc()
    {
        return self::group('misc', [
            'title' => 'Miscellaneous',
            'description' => 'All the settings, that didn\t find their own place.',
            'icon' => 'fa-cogs'
        ]);
    }
}
