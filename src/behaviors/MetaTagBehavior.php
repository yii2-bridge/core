<?php
/**
 * Created by PhpStorm.
 * User: rocketscientist
 * Date: 28.04.2018
 * Time: 16:29
 */

namespace naffiq\bridge\behaviors;

use naffiq\bridge\models\MetaModel;
use naffiq\bridge\models\MetaTag;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class MetaTagBehavior extends Behavior
{
    /**
     * Название колонки заголовка вызываемой модели ($this->owner),
     * для случая, если для данной модели не будет указано Meta title
     *
     * @var string|null
     */
    public $titleColumn;

    /**
     * Название колонки описания вызываемой модели ($this->owner),
     * для случая, если для данной модели не будет указано Meta description
     *
     * @var string|null
     */
    public $descriptionColumn;

    /**
     * Название колонки изображения вызываемой модели ($this->owner),
     * для случая, если для данной модели не будет указано Meta og:image
     *
     * @var string|null
     */
    public $imageColumn;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'saveMetaTags',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveMetaTags'
        ];
    }

    /**
     * Сохранение мета-тегов для вызываемой модели ($this->owner)
     *
     * @return mixed
     */
    public function saveMetaTags()
    {
        if(!$this->owner->metaModel) {
            $metaTag = $this->createMetaTag();
            return $this->createMetaModel($metaTag);
        }

        return $this->owner->metaTag->save();
    }

    /**
     * @return null|MetaModel
     */
    public function getMetaModel()
    {
        return MetaModel::findOne(['model' => $this->owner::className(), 'model_id' => $this->owner->id]);
    }

    /**
     * @return MetaTag
     */
    public function getMetaTag()
    {
        return $this->metaModel->metaTag ?? new MetaTag();
    }

    /**
     * Создание нового объекта класса MetaTag
     *
     * @return MetaTag
     */
    private function createMetaTag()
    {
        $metaTag = new MetaTag();
        $metaTag->save();
        return $metaTag;
    }

    /**
     * Создание нового объекта класса MetaModel,
     * который связывает вызываемую модель ($this->owner) с MetaTag
     *
     * @param MetaTag $metaTag
     * @return boolean
     */
    private function createMetaModel(MetaTag $metaTag)
    {
        $metaModel = new MetaModel();
        $metaModel->model = $this->owner::className();
        $metaModel->model_id = $this->owner->id;
        $metaModel->meta_tag_id = $metaTag->id;

        return $metaModel->save();
    }
}