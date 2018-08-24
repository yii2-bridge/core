<?php

namespace Bridge\Core\Models;

use Bridge\Core\Behaviors\BridgeUploadImageBehavior;
use Bridge\Core\Assets\AdminAsset;
use Bridge\Core\Behaviors\TranslationBehavior;
use Bridge\Core\BridgeModule;
use Bridge\Core\Models\Query\SettingsQuery;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\InvalidCallException;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii2tech\ar\position\PositionBehavior;
use yii\db\Exception;

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
 *
 * @property SettingsTranslation[] $settingsTranslations
 * @property SettingsTranslation $translation
 * @method  SettingsTranslation getTranslation($languageCode = null, $cacheKey = null)
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
            [['type', 'group_id'], 'integer'],
            [['title', 'key'], 'string', 'max' => 255],
            [['key'], 'unique', 'targetAttribute' => ['key']],
            ['value', 'safe'],
            ['value', 'image', 'extensions' => 'jpg, jpeg, gif, png', 'on' => ['create', 'update'], 'when' => ['\Bridge\Core\Models\Settings', 'validateImageValue'], 'whenClient' => "function (attribute, value) {
        return $('.js-setting-value').attr('type') == 'file';
    }"],
        ];
    }

    public static function validateImageValue($model) {
        return $model->type == static::TYPE_IMAGE;
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
            ],
            'translation' => [
                'class' => TranslationBehavior::class,
                'translationModelClass' => SettingsTranslation::class,
                'translationModelRelationColumn' => 'settings_id'
            ],
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

        $isSettingTranslated = SettingsTranslation::find()->where(['settings_id' => $this->id])->exists();

        if (!$isSettingTranslated) {
            self::createTranslations($this);
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
     * @throws InvalidArgumentException when settings with key provided wasn't found
     */
    public static function get($key)
    {
        if (!empty(static::$prevSettings[$key])) {
            $model = static::$prevSettings[$key];
        } else {
            $model = static::find()->key($key)->one();
        }

        if (empty($model)) {
            throw new InvalidArgumentException("Setting with key '{$key}' wasn't found. Try creating it first or run getOrCreate method");
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
        } catch (InvalidArgumentException $e) {
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
            if (\Yii::$app->getModule('admin')->settingsCaching) {
                $cacheKey = \Yii::$app->getModule('admin')->settingsCacheKey;
                if ($this->getTranslation(null, $cacheKey . '-' . $this->key)->value) {
                    return $this->getTranslation(null, $cacheKey . '-' . $this->key)->getUploadUrl('value');
                }
            } else {
                if ($this->translation->value) {
                    return $this->translation->getUploadUrl('value');
                }
            }

            $bundle = \Yii::$app->assetManager->getBundle(AdminAsset::class);
            return \Yii::$app->assetManager->getAssetUrl($bundle, 'avatar@2x.jpg');
        }

        if(\Yii::$app->getModule('admin')->settingsCaching) {
            $cacheKey = \Yii::$app->getModule('admin')->settingsCacheKey;
            return (string) $this->getTranslation(null, $cacheKey . '-' . $this->key)->value;
        } else {
            return (string) $this->translation->value;
        }
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
        if(\Yii::$app->getModule('admin')->settingsCaching) {
            $cacheKey = \Yii::$app->getModule('admin')->settingsCacheKey;
            $group = \Yii::$app->cache->getOrSet($cacheKey . '_group-' . $groupKey, function () use ($groupKey) {
                return SettingsGroup::find()->where(['key' => $groupKey])->one();
            }, 86400);
        } else {
            $group = SettingsGroup::find()->where(['key' => $groupKey])->one();
        }

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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSettingsTranslations()
    {
        return $this->hasMany(SettingsTranslation::class, ['settings_id' => 'id']);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes){
        parent::afterSave($insert, $changedAttributes);

        /** Кэшируем настройку */
        if(\Yii::$app->getModule('admin')->settingsCaching) {
            $cacheKey = \Yii::$app->getModule('admin')->settingsCacheKey;
            \Yii::$app->cache->set($cacheKey . '-' . $this->key, $this, 86400);
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();

        /** Удаляем настройку из кэша */
        if(\Yii::$app->getModule('admin')->settingsCaching) {
            $cacheKey = \Yii::$app->getModule('admin')->settingsCacheKey;
            \Yii::$app->cache->delete($cacheKey . '-' . $this->key);
        }
    }

    /**
     * Creates setting translations
     *
     * @param Settings $model
     * @return bool
     */
    private static function createTranslations(Settings $model)
    {
        $data = [];

        foreach (Yii::$app->urlManager->languages as $label => $code) {
            $data[] = [
                'lang' => $code,
                'settings_id' => $model->id,
                'value' => $model->value
            ];
        }

        try {
            Yii::$app->db
                ->createCommand()
                ->batchInsert('settings_translations', ['lang', 'settings_id', 'value'], $data)
                ->execute();

            return true;
        } catch (Exception $exception) {
            // TODO: Логировать ошибку создание переводов для настроек
        }

        return false;
    }
}
