<?php

/* @var $this yii\web\View */
/* @var $model Bridge\Core\Models\SettingsGroup */

$this->title = Yii::t('bridge', 'Update Settings Group') . ': ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('bridge', 'Settings'), 'url' => ['/admin/settings/index/', 'group' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('bridge', 'Update');
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>


