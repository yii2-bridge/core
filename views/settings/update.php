<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Settings */

$this->title = 'Update Settings: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Settings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<h1><?= $this->title ?></h1>

<?= $this->render('_form', [
    'model' => $model,
]) ?>


