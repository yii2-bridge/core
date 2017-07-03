<?php

/* @var $this yii\web\View */
/* @var $model app\models\Works */

$this->title = 'Update Works: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Works', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<h1><?= $this->title ?></h1>

<?= $this->render('_form', [
    'model' => $model,
]) ?>


