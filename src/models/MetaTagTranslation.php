<?php

namespace Bridge\Core\Models;

use Bridge\Core\Models\Query\MetaTagTranslationQuery;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "meta_tag_translations".
 *
 * @property integer $id
 * @property string $lang
 * @property integer $meta_tag_id
 * @property string $title
 * @property string $description
 * @property string $keywords
 * @property string $image
 *
 * @property MetaTag $metaTag
 *
 * @method string getThumbUploadPath($attribute, $profile = 'thumb', $old = false)
 * @method string|null getThumbUploadUrl($attribute, $profile = 'thumb')
 * @method string|null getUploadPath($attribute, $old = false) Returns file path for the attribute.
 * @method string|null getUploadUrl($attribute) Returns file url for the attribute.
 * @method bool sanitize($filename) Replaces characters in strings that are illegal/unsafe for filename.
 */
class MetaTagTranslation extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meta_tag_translations';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lang'], 'required'],
            [['meta_tag_id'], 'integer'],
            [['description', 'keywords'], 'string'],
            [['lang', 'title'], 'string', 'max' => 255],
            [['image'], 'file', 'on' => ['create', 'update', 'default'], 'extensions' => ['gif', 'jpg', 'png', 'jpeg']],
            [['lang', 'meta_tag_id'], 'unique', 'targetAttribute' => ['lang', 'meta_tag_id'], 'message' => 'The combination of Lang and Meta Tag ID has already been taken.'],
            [['meta_tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => MetaTag::class, 'targetAttribute' => ['meta_tag_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('bridge', 'ID'),
            'lang' => Yii::t('bridge', 'Lang'),
            'meta_tag_id' => Yii::t('bridge', 'Meta Tag ID'),
            'title' => Yii::t('bridge', 'Title'),
            'description' => Yii::t('bridge', 'Description'),
            'keywords' => Yii::t('bridge', 'Keywords'),
            'image' => Yii::t('bridge', 'Image'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMetaTag()
    {
        return $this->hasOne(MetaTag::class, ['id' => 'meta_tag_id']);
    }

    /**
     * @inheritdoc
     * @return MetaTagTranslationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MetaTagTranslationQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'imageUpload' => [
                'class' => '\Bridge\Core\Behaviors\BridgeUploadImageBehavior',
                'attribute' => 'image',
                'isTranslation' => true,
                'instanceByName' => true,
                'path' => '@webroot/media/meta_tag_translations/{id}',
                'url' => '@web/media/meta_tag_translations/{id}',
                'scenarios' => ['create', 'update', 'default'],
                // TODO: Узнать размер превью для соц. сетей
                'thumbs' => ['thumb' => ['width' => 200, 'height' => 200, 'quality' => 90], 'preview' => ['width' => 50, 'height' => 50, 'quality' => 90]],
            ],
        ];
    }
}
