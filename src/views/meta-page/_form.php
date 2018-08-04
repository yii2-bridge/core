<?php

use yii\bootstrap\Html;
use naffiq\bridge\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model Bridge\Core\Models\MetaPage */
/* @var $form naffiq\bridge\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-8">
        <?= $form->translate($model->metaTag, '@app/vendor/naffiq/yii2-bridge/src/views/meta-page/_form-meta-tags') ?>

    </div>

    <div class="col-md-4">
        <?= $form->field($model, 'meta_tag_id')->textInput()->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'module')->textInput(['maxlength' => true, 'disabled' => true]) ?>

        <?= $form->field($model, 'controller')->textInput(['maxlength' => true, 'disabled' => true]) ?>

        <?= $form->field($model, 'action')->textInput(['maxlength' => true, 'disabled' => true]) ?>
    </div>
</div>

<div class="form-group">
    <?= Html::submitButton($model->isNewRecord ? Yii::t('bridge', 'Create') : Yii::t('bridge', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>
