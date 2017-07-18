<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 7/19/2017
 * Time: 1:56 AM
 */

namespace naffiq\bridge\widgets\columns;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQueryInterface;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;

/**
 * Class TitledImageColumn
 *
 * In addition to `ImageColumn`'s image, it adds `$titleAttribute` to column.
 *
 * @package naffiq\bridge\widgets\columns
 */
class TitledImageColumn extends ImageColumn
{
    /**
     * @var string
     */
    public $imageAttribute;

    /**
     * @var string|array|callable works just like ordinary `value` option but for title only
     */
    public $value;

    /**
     * Gets title
     *
     * @param mixed|Model $model
     * @param mixed $key
     * @param int $index
     * @return mixed|null
     */
    protected function getTitleValue($model, $key, $index)
    {
        if ($this->attribute !== null) {
            if (is_string($this->attribute)) {
                return ArrayHelper::getValue($model, $this->attribute);
            } else {
                return call_user_func($this->attribute, $model, $key, $index, $this);
            }
        } elseif ($this->attribute !== null) {
            return ArrayHelper::getValue($model, $this->attribute);
        }
        return null;
    }

    /**
     * Adds attribute (title) to image defined in `$imageAttribute`
     *
     * @param mixed|Model $model
     * @param mixed $key
     * @param int $index
     * @return string
     */
    public function getDataCellValue($model, $key, $index)
    {
        return $this->getImageTag($this->getImageUrl($model, $key, $index, $this->imageAttribute))
            . $this->getTitleValue($model, $key, $index);
    }

}