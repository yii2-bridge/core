<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 7/19/2017
 * Time: 1:35 AM
 */

namespace naffiq\bridge\widgets\columns;


use mongosoft\file\UploadImageBehavior;
use yii\base\InvalidParamException;
use yii\base\Model;
use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class ImageColumn
 *
 * Since yii2-bridge recommends using `mongosoft\file\UploadImageBehavior` heavily under the hood,
 * this class allows you to execute model's behavior attached to attribute specified.
 *
 * Otherwise, `$model` doesn't have behavior attached it just wraps value into `Html::img` tag, so be sure
 * to return img url
 *
 * @package naffiq\bridge\widgets\columns
 */
class ImageColumn extends DataColumn
{
    /**
     * @var string Image profile to be passed to `ImageColumn::$uploadBehaviorMethod` method as `profile` attribute
     * @see ImageColumn::$uploadBehaviorMethod
     */
    public $imageProfile = 'preview';

    /**
     * @var string Method to run inside `UploadImageBehavior`
     */
    public $uploadBehaviorMethod = 'getThumbUploadUrl';

    /**
     * @var string CSS-class to be appended to generated `<img>` tag
     */
    public $imageClass = 'img-circle img-preview';

    public $format = 'raw';

    /**
     * Checks `$model` if it has `\mongosoft\file\UploadImageBehavior` and returns its' `getThumbUploadUrl()` method
     * result.
     *
     * If no UploadImageBehavior detected in `$model`, then it returns `DataColumn::getDataCellValue()` result
     * and wraps it with
     *
     * @param mixed|Model $model
     * @param mixed $key
     * @param int $index
     * @return mixed
     */
    public function getDataCellValue($model, $key, $index)
    {
        return $this->getImageTag($this->getImageUrl($model, $key, $index, $this->attribute));
    }

    /**
     * Generated
     *
     * @param $imageUrl
     * @return string
     */
    protected function getImageTag($imageUrl)
    {
        return Html::img($imageUrl, ['class' => $this->imageClass]);
    }

    /**
     * Returns the image cell value by attribute.
     *
     * @param mixed $model the data model
     * @param mixed $key the key associated with the data model
     * @param int $index the zero-based index of the data model among the models array returned by [[GridView::dataProvider]].
     * @param null|string image attribute for child classes
     * @return string the data cell value
     */
    protected function getAttributeCellValue($model, $key, $index, $attribute = null)
    {
        if ($this->value !== null) {
            if (is_string($this->value)) {
                return ArrayHelper::getValue($model, $this->value);
            } else {
                return call_user_func($this->value, $model, $key, $index, $this);
            }
        } elseif ($attribute !== null) {
            return ArrayHelper::getValue($model, $attribute);
        }
        return null;
    }

    /**
     * Gets image url by detecting behavior or passing it to `getImageCellValue` method
     *
     * @param mixed|Model $model the data model
     * @param mixed $key the key associated with the data model
     * @param int $index the zero-based index of the data model among the models array returned by [[GridView::dataProvider]].
     * @param null|string image attribute for child classes
     * @return string image url
     */
    protected function getImageUrl($model, $key, $index, $attribute)
    {
        $imgUrl = false;

        if (is_object($model) && method_exists($model, 'getBehaviors')) {

            foreach ($model->getBehaviors() as $behavior) {
                if ($behavior::class == UploadImageBehavior::class && $behavior->attribute == $attribute) {
                    /**
                     * @var $behavior UploadImageBehavior
                     */
                    if ($behavior->hasMethod($this->uploadBehaviorMethod)) {
                        $imgUrl = call_user_func([$behavior, $this->uploadBehaviorMethod], $attribute, $this->imageProfile);
                    } else {
                        throw new InvalidParamException(
                            '`\mongosoft\file\UploadImageBehavior` doesn\'t have method '
                            . $this->uploadBehaviorMethod
                        );
                    }
                    break;
                }
            }
        }

        if (!$imgUrl) {
            $imgUrl = $this->getAttributeCellValue($model, $key, $index, $attribute);
        }

        return $imgUrl;
    }
}