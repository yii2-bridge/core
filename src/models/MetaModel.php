<?php

namespace Bridge\Core\Models;

use Bridge\Core\Models\Query\MetaModelQuery;
use Yii;
use yii\db\ActiveRecord;

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
class MetaModel extends ActiveRecord
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
        return $this->hasOne(MetaTag::class, ['id' => 'meta_tag_id']);
    }

    /**
     * @inheritdoc
     * @return MetaModelQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MetaModelQuery(get_called_class());
    }

    /**
     * Создаем новый объект класса MetaModel,
     * который связывает вызываемую модель ($this->owner) с MetaTag
     *
     * @param ActiveRecord $model
     * @param MetaTag $metaTag
     * @return MetaModel
     */
    public static function create(ActiveRecord $model, MetaTag $metaTag)
    {
        $metaModel = new MetaModel();

        $metaModel->model = get_class($model);
        $metaModel->model_id = $model->id;
        $metaModel->meta_tag_id = $metaTag->id;

        $metaModel->save();

        return $metaModel;
    }
}
