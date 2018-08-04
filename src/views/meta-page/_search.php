<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model Bridge\Core\Models\Search\MetaPageSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="meta-page-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'meta_tag_id') ?>

    <?= $form->field($model, 'module') ?>

    <?= $form->field($model, 'controller') ?>

    <?= $form->field($model, 'action') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('bridge', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('bridge', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
