<?php

use naffiq\bridge\widgets\ActiveForm;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model \naffiq\bridge\models\SettingsGroup */
?>
<?php $form = ActiveForm::begin(); ?>

<div class="row">
    <div class="col-lg-8">
        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'icon')->fontAwesome() ?>


        <?= $form->field($model, 'description')->textarea() ?>
    </div>
</div>
<div class="form-group clearfix">
    <?= Html::submitButton(\Yii::t('bridge', $model->isNewRecord ? 'Create' : 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
