<?php
/**
 * @var $languageCode string
 * @var $model \naffiq\bridge\models\MetaTag
 * @var $form \naffiq\bridge\widgets\ActiveForm
 */
$translationModel = $model->getTranslation($languageCode);
?>

<?= $form->field($translationModel, '['.$languageCode.']meta_tag_id')->hiddenInput()->label(false) ?>
<?= $form->field($translationModel, '['.$languageCode.']lang')->hiddenInput()->label(false)  ?>
<?= $form->field($translationModel, '['.$languageCode.']title')->textInput() ?>
<?= $form->field($translationModel, '['.$languageCode.']description')->richTextArea() ?>
<?= $form->field($translationModel, '['.$languageCode.']image')->imageUpload() ?>
