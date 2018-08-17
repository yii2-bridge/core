<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model Bridge\Core\Models\MetaPage */

$this->title = $model->metaTag->translation->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('bridge', 'Meta Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['contextMenuItems'] = [
    ['update', 'id' => $model->id]
];
?>
<div class="row">
    <div class="col-lg-8 detail-view-wrap">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'id',
//            'meta_tag_id',
            'module',
            'controller',
            'action',
            [
                'attribute' => 'metaTag.translation.title',
                'label' => $model->metaTag->translation->getAttributeLabel('title')
            ],
            [
                'attribute' => 'metaTag.translation.description',
                'label' => $model->metaTag->translation->getAttributeLabel('description'),
                'format' => 'raw',
            ],
            [
                'attribute' => 'metaTag.translation.image',
                'label' => $model->metaTag->translation->getAttributeLabel('image'),
                'value' => $model->metaTag->translation->getThumbUploadUrl('image'),
                'format' => 'image',
            ],
        ],
    ]) ?>
    </div>
</div>