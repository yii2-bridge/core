<?php

use yii\bootstrap\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Works */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Works', 'url' => ['index']];
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
            'title',
            'description:ntext',
            'excerpt',
            'link',
            'thumb',
            'thumb_2x',
            'created_at',
            'updated_at',
        ],
    ]) ?>
    </div>
</div>