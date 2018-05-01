<?php

namespace naffiq\bridge\models;

use naffiq\bridge\models\query\MetaTagTranslationQuery;
use Yii;

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
class MetaTagTranslation extends \yii\db\ActiveRecord
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
            [['image'], 'file', 'on' => ['create', 'update'], 'extensions' => ['gif', 'jpg', 'png', 'jpeg']],
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
            'id' => 'ID',
            'lang' => 'Lang',
            'meta_tag_id' => 'Meta Tag ID',
            'title' => 'Title',
            'description' => 'Description',
            'keywords' => 'Keywords',
            'image' => 'Image',
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
            // TODO: Переписать поведение загрузки изображении для мультиязычности
            /*'imageUpload' => [
                'class' => 'mongosoft\file\UploadImageBehavior',
                'attribute' => 'image',
                'path' => '@webroot/media/meta_tag_translations/{id}',
                'url' => '@web/media/meta_tag_translations/{id}',
                'scenarios' => ['create', 'update'],
                'thumbs' => ['thumb' => ['width' => 200, 'height' => 200, 'quality' => 90], 'preview' => ['width' => 50, 'height' => 50, 'quality' => 90]],
            ],*/
        ];
    }
}
