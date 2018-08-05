<?php

/* @var $this yii\web\View */
/* @var $model Bridge\Core\Models\Settings */

$this->title = Yii::t('bridge', 'Create Settings');
$this->params['breadcrumbs'][] = ['label' => Yii::t('bridge', 'Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>

