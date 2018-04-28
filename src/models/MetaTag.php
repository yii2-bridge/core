<?php

namespace naffiq\bridge\models;

use naffiq\bridge\models\query\MetaTagQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "meta_tags".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property MetaModel[] $metaModels
 * @property MetaTagTranslation[] $metaTagTranslations
 */
class MetaTag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meta_tags';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => Yii::t('bridge', 'Created At'),
            'updated_at' => Yii::t('bridge', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMetaModels()
    {
        return $this->hasMany(MetaModel::className(), ['meta_tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMetaTagTranslations()
    {
        return $this->hasMany(MetaTagTranslation::className(), ['meta_tag_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return MetaTagQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MetaTagQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('CURRENT_TIMESTAMP()'),
            ],
            'translation' => [
                'class' => 'naffiq\bridge\behaviors\TranslationBehavior',
                'translationModelClass' => MetaTagTranslation::class,
                'translationModelRelationColumn' => 'meta_tag_id'
            ]
        ];
    }
}
