<?php

/* @var $this yii\web\View */
/* @var $model Bridge\Core\Models\Settings */

$this->title = Yii::t('bridge', 'Update Settings') . ': ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('bridge', 'Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('bridge', 'Update');
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>


