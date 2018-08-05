<?php

/* @var $this yii\web\View */
/* @var $model Bridge\Core\Models\MetaPage */

$this->title = Yii::t('bridge', 'Update Meta Page: ') . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('bridge', 'Meta Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('bridge', 'Update');
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>


