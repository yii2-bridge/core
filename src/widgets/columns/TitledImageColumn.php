<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 7/19/2017
 * Time: 1:56 AM
 */

namespace Bridge\Core\Widgets\Columns;

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
 * @package Bridge\Core\Widgets\Columns
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
            . ' ' . $this->getAttributeCellValue($model, $key, $index, $this->attribute);
    }
}