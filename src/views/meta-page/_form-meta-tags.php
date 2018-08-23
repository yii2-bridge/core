<?php
/**
 * @var $languageCode string
 * @var $model \Bridge\Core\Models\MetaTag
 * @var $form \Bridge\Core\Widgets\ActiveForm
 */
$translationModel = $model->getTranslation($languageCode);
?>

<div class="row">
    <div class="col-md-12">
        <?= $form->field($translationModel, '[' . $languageCode . ']meta_tag_id')->hiddenInput()->label(false) ?>
        <?= $form->field($translationModel, '[' . $languageCode . ']lang')->hiddenInput()->label(false) ?>
        <?= $form->field($translationModel, '[' . $languageCode . ']title')->textInput() ?>
    </div>
    <div class="col-md-7">
        <?= $form->field($translationModel, '[' . $languageCode . ']description')->richTextArea(
            [
                'preset' => 'custom'
            ],
            [
                'height' => 225,
                'toolbar' => [
                    [
                        'name' => 'document',
                        'items' => ['Source']
                    ]
                ]
            ]) ?>
    </div>
    <div class="col-md-5">
        <?= $form->field($translationModel, '[' . $languageCode . ']image')->imageUpload() ?>
    </div>
</div>
