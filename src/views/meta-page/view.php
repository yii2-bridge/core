<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model naffiq\bridge\models\MetaPage */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('bridge', 'Meta Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['contextMenuItems'] = [
    ['update', 'id' => $model->id],
    ['delete', 'id' => $model->id]
];
?>
<div class="row">
    <div class="col-lg-8 detail-view-wrap">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'meta_tag_id',
            'module',
            'controller',
            'action',
        ],
    ]) ?>
    </div>
</div>