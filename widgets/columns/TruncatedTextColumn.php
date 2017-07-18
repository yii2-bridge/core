<?php

namespace naffiq\bridge\widgets\columns;

use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

/**
 * Class TruncatedTextColumn
 *
 * Truncate text of grid column data.
 * Example usage:
 * ```php
 * <?php
 *
 * <?= \yii\widgets\GridView::widget([
 *      'dataProvider' => $dataProvider,
 *      'columns' => [
 *          [
 *              'class' => '\naffiq\bridge\widgets\columns\TrimmedTextColumn',
 *              'attribute' => 'description',
 *              'truncateLength' => 150, // Control length of truncated string, default is 100
 *          ]
 *      ]
 * ])
 *
 * ```
 *
 * @package naffiq\bridge\widgets\columns
 */
class TruncatedTextColumn extends DataColumn
{
    /**
     * @var int
     */
    public $truncateLength = 100;

    /**
     * Strips value of `$model->$attribute` of tags and truncates it to `$truncateLength` symbols
     *
     * @param mixed $model
     * @param mixed $key
     * @param int $index
     * @return string
     */
    public function getDataCellValue($model, $key, $index)
    {
        $value = ArrayHelper::getValue($model, $this->attribute);
        return StringHelper::truncate(strip_tags($value), $this->truncateLength);
    }
}