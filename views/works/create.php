<?php

/* @var $this yii\web\View */
/* @var $model app\models\Works */

$this->title = 'Create Works';
$this->params['breadcrumbs'][] = ['label' => 'Works', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= $this->title ?></h1>
<?= $this->render('_form', [
    'model' => $model,
]) ?>

