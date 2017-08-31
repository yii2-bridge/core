<?php

/* @var $this yii\web\View */
/* @var $model naffiq\bridge\models\Settings */

$this->title = Yii::t('bridge', 'Create Settings');
$this->params['breadcrumbs'][] = ['label' => Yii::t('bridge', 'Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>

