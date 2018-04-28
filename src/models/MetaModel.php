<?php

namespace naffiq\bridge\models;

use naffiq\bridge\models\query\MetaModelQuery;
use Yii;

/**
 * This is the model class for table "meta_models".
 *
 * @property integer $id
 * @property string $model
 * @property integer $model_id
 * @property integer $meta_tag_id
 *
 * @property MetaTag $metaTag
 */
class MetaModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meta_models';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model', 'model_id', 'meta_tag_id'], 'required'],
            [['model_id', 'meta_tag_id'], 'integer'],
            [['model'], 'string', 'max' => 255],
            [['meta_tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => MetaTag::className(), 'targetAttribute' => ['meta_tag_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model' => Yii::t('bridge', 'Model class name'),
            'model_id' => Yii::t('bridge', 'Model item ID'),
            'meta_tag_id' => Yii::t('bridge', 'Meta Tag ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMetaTag()
    {
        return $this->hasOne(MetaTag::className(), ['id' => 'meta_tag_id']);
    }
    
    /**
     * @inheritdoc
     * @return MetaModelQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MetaModelQuery(get_called_class());
    }

}
