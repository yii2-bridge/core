<?php

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Settings */

$this->title = 'Create Settings';
$this->params['breadcrumbs'][] = ['label' => 'Settings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= $this->title ?></h1>

<?= $this->render('_form', [
    'model' => $model,
]) ?>

