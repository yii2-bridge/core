<?php

/* @var $this yii\web\View */
/* @var $model Bridge\Core\Models\SettingsGroup */

$this->title = Yii::t('bridge', 'Create Settings Group');
$this->params['breadcrumbs'][] = ['label' => Yii::t('bridge', 'Settings'), 'url' => ['/admin/settings/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>

