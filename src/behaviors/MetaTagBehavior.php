<?php
/**
 * @property MetaModel $metaModel
 * @property MetaTag $metaTag
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
            $metaTag = MetaTag::create();
            return MetaModel::create($this->owner, $metaTag);
        }

        return $this->owner->metaTag->save();
    }

    /**
     * @return null|MetaModel
     */
    public function getMetaModel()
    {
        return MetaModel::findOne(['model' => get_class($this->owner), 'model_id' => $this->owner->id]);
    }

    /**
     * @return MetaTag
     */
    public function getMetaTag()
    {
        return $this->metaModel->metaTag ?? new MetaTag();
    }
}