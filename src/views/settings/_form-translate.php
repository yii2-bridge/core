<?php
/**
 * @var $languageCode string
 * @var $model \Bridge\Core\Models\Settings
 * @var $form \Bridge\Core\Widgets\ActiveForm
 */

use Bridge\Core\Models\Settings;

$translationModel = $model->getTranslation($languageCode);
?>

<?php if ($model->type == Settings::TYPE_TEXT) : ?>
    <?= $form->field($translationModel, '['.$languageCode.']settings_id')->hiddenInput()->label(false) ?>
    <?= $form->field($translationModel, '['.$languageCode.']lang')->hiddenInput()->label(false)  ?>
    <?= $form->field($translationModel, '['.$languageCode.']value')->richTextArea(['options' => ['rows' => 6]]) ?>
<?php elseif ($model->type == Settings::TYPE_IMAGE) : ?>
    <?= $form->field($translationModel, '['.$languageCode.']settings_id')->hiddenInput()->label(false) ?>
    <?= $form->field($translationModel, '['.$languageCode.']lang')->hiddenInput()->label(false)  ?>
    <?= $form->field($translationModel, '['.$languageCode.']value')->imageUpload(['options' => ['class' => 'js-setting-value']]) ?>
<?php elseif ($model->type == Settings::TYPE_SWITCH): ?>
    <?= $form->field($translationModel, '['.$languageCode.']settings_id')->hiddenInput()->label(false) ?>
    <?= $form->field($translationModel, '['.$languageCode.']lang')->hiddenInput()->label(false)  ?>
    <?= $form->field($translationModel, '['.$languageCode.']value')->switchInput() ?>
<?php elseif ($model->type == Settings::TYPE_MAP): ?>
    <?= $form->field($translationModel, '['.$languageCode.']settings_id')->hiddenInput()->label(false) ?>
    <?= $form->field($translationModel, '['.$languageCode.']lang')->hiddenInput()->label(false)  ?>
    <?= $form->field($translationModel, '['.$languageCode.']value')->map() ?>
<?php else: ?>
    <?= $form->field($translationModel, '['.$languageCode.']settings_id')->hiddenInput()->label(false) ?>
    <?= $form->field($translationModel, '['.$languageCode.']lang')->hiddenInput()->label(false)  ?>
    <?= $form->field($translationModel, '['.$languageCode.']value')->textInput(['class' => 'form-control js-setting-value']) ?>
<?php endif; ?>
