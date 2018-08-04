<?php
/**
 * Created by PhpStorm.
 * User: naffiq
 * Date: 8/2/2017
 * Time: 11:57 PM
 */

namespace Bridge\Core\Widgets;

/**
 * Class ActiveForm
 *
 * Provides CMF-specific input field
 * @see ActiveField
 *
 * @package Bridge\Core\Widgets
 */
class ActiveForm extends \yii\widgets\ActiveForm
{
    /**
     * @inheritdoc
     */
    public $fieldClass = '\Bridge\Core\Widgets\ActiveField';

    /**
     * @inheritdoc
     * @return ActiveField
     */
    public function field($model, $attribute, $options = [])
    {
        return parent::field($model, $attribute, $options);
    }

    public function translate($model, $viewName, $languages = null)
    {
        return TranslationFormWidget::widget([
            'form' => $this,
            'model' => $model,
            'languages' => $languages,
            'viewName' => $viewName
        ]);
    }

    public function metaTags($model, $viewName = '@app/vendor/naffiq/yii2-bridge/src/views/meta-page/_form-meta-tags', $languages = null)
    {
        return MetaTagsFormWidget::widget([
            'form' => $this,
            'model' => $model,
            'languages' => $languages,
            'viewName' => $viewName
        ]);
    }
}