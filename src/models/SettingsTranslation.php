<?php

namespace Bridge\Core\Models;

use Bridge\Core\Assets\AdminAsset;
use Bridge\Core\Models\Query\SettingsTranslationQuery;
use Yii;
use yii\web\JsExpression;

/**
 * This is the model class for table "settings_translations".
 *
 * @property integer $id
 * @property string $lang
 * @property string $settings_id
 * @property string $value
 * 
 * @property Settings $setting
 *
 * @method string getThumbUploadPath($attribute, $profile = 'thumb', $old = false)
 * @method string|null getThumbUploadUrl($attribute, $profile = 'thumb')
 * @method string|null getUploadPath($attribute, $old = false) Returns file path for the attribute.
 * @method string|null getUploadUrl($attribute) Returns file url for the attribute.
 */
class SettingsTranslation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'settings_translations';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lang'], 'required'],
            [['lang'], 'string', 'max' => 255],
            [['settings_id'], 'integer'],
            ['value', 'safe'],
            [['lang', 'settings_id'], 'unique', 'targetAttribute' => ['lang', 'settings_id'], 'message' => Yii::t('bridge', 'The combination of {lang} and {settings_id} has already been taken.', [
                'lang' => Yii::t('bridge', 'Language'),
                'settings_id' => Yii::t('bridge', 'Settings ID'),
            ])],
            [['settings_id'], 'exist', 'skipOnError' => true, 'targetClass' => Settings::class, 'targetAttribute' => ['settings_id' => 'id']],
            ['value', 'image', 'extensions' => 'jpg, jpeg, gif, png', 'on' => ['create', 'update', 'default'], 'when' => ['\Bridge\Core\Models\SettingsTranslation', 'validateImageValue'], 'whenClient' => "function (attribute, value) {
        return $('.js-setting-value').attr('type') == 'file';
    }"],
        ];
    }

    public static function validateImageValue($model) {
        return $model->setting->type == Settings::TYPE_IMAGE;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lang' => Yii::t('bridge', 'Language'),
            'settings_id' => Yii::t('bridge', 'Settings ID'),
            'value' => Yii::t('bridge', 'Value'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSetting()
    {
        return $this->hasOne(Settings::class, ['id' => 'settings_id']);
    }

    /**
     * @inheritdoc
     * @return SettingsTranslationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SettingsTranslationQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
        if ($this->setting->type == Settings::TYPE_IMAGE) {
            $this->attachBehavior('uploadImage', [
                'class' => '\Bridge\Core\Behaviors\BridgeUploadImageBehavior',
                'attribute' => 'value',
                'isTranslation' => true,
                'instanceByName' => true,
                'path' => '@webroot/media/settings/{setting.id}/{lang}',
                'url' => '@web/media/settings/{setting.id}/{lang}',
                'scenarios' => ['create', 'update', 'default'],
                'thumbs' => ['preview' => ['width' => 50, 'height' => 50]],
            ]);
        }
    }

    /**
     * Return setting's value for casting
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->setting->type == Settings::TYPE_IMAGE) {
            if ($this->value) {
                return $this->getUploadUrl('value');
            }

            $bundle = \Yii::$app->assetManager->getBundle(AdminAsset::class);
            return \Yii::$app->assetManager->getAssetUrl($bundle, 'avatar@2x.jpg');
        }
        return (string) $this->value;
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes){
        parent::afterSave($insert, $changedAttributes);

        \Yii::$app->cache->set('bridge_settings-' . $this->setting->key . '-' . $this->lang, $this, 86400);
        \Yii::$app->cache->set('bridge_settings-' . $this->setting->key . '-translated', true);
    }
}
