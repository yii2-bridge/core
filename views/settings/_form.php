<?php

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use naffiq\bridge\widgets\TinyMce;
use naffiq\bridge\models\Settings;

/* @var $this yii\web\View */
/* @var $model Settings */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(); ?>

<div class="row">
    <div class="col-lg-5">
        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

        <?php if ($model->type == Settings::TYPE_TEXT) : ?>
            <?= $form->field($model, 'value')->widget(TinyMce::className(), [
                'options' => ['rows' => 6],
            ]) ?>
        <?php elseif ($model->type == Settings::TYPE_IMAGE) : ?>
            <?= Html::img($model->getUploadUrl('value'), ['class' => 'img-thumbnail']) ?>
            <?= $form->field($model, 'value')->fileInput(['accept' => 'image/*']) ?>
        <?php else: ?>
            <?= $form->field($model, 'value')->textInput() ?>
        <?php endif; ?>
    </div>
    <div class="col-md-4">

        <button type="button" class="btn btn-default"
                data-toggle="collapse" data-target="#advancedSettings" aria-expanded="false"
                aria-controls="advancedSettings">
            Advanced settings
        </button>

        <div id="advancedSettings" class="collapse">
            <br/>

            <div class="alert alert-danger">
                <b>Warning!</b> Don't edit, if you are not familiar what those do!
            </div>

            <?= $form->field($model, 'key')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'type')->dropDownList(Settings::$types) ?>

            <?= $form->field($model, 'type_settings')->textarea(['rows' => 6]) ?>
        </div>
    </div>
</div>
<div class="form-group clearfix">
    <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
