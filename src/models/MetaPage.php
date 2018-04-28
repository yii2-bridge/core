<?php

namespace naffiq\bridge\models;

use naffiq\bridge\models\query\MetaPageQuery;
use Yii;

/**
 * This is the model class for table "meta_pages".
 *
 * @property integer $id
 * @property integer $meta_tag_id
 * @property string $module
 * @property string $controller
 * @property string $action
 *
 * @property MetaTag $metaTag
 */
class MetaPage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meta_pages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['meta_tag_id', 'module', 'controller', 'action'], 'required'],
            [['meta_tag_id'], 'integer'],
            [['module', 'controller', 'action'], 'string', 'max' => 255],
            [['meta_tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => MetaTag::className(), 'targetAttribute' => ['meta_tag_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('bridge', 'ID'),
            'meta_tag_id' => Yii::t('bridge', 'Meta Tag ID'),
            'module' => Yii::t('bridge', 'Module name'),
            'controller' => Yii::t('bridge', 'Controller name'),
            'action' => Yii::t('bridge', 'Action name'),
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
     * @return MetaPageQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MetaPageQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        /**
         * Сохранение мета-тегов
         */
        $this->metaTag->save();
    }
}
