<?php

use himiklab\sortablegrid\SortableGridView;
use yii\helpers\Url;
use yii2tech\admin\grid\ActionColumn;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\WorksSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Works';
$this->params['breadcrumbs'][] = $this->title;
$this->params['contextMenuItems'] = [
    ['create']
];
?>

<div class="pull-right">
    <a href="<?= Url::to(['create']) ?>" class="btn btn-success"><i class="fa fa-plus"></i> Create</a>
</div>

<h1><?= $this->title ?></h1>

<?= SortableGridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'tableOptions' => ['class' => 'table table-striped'],
    'options' => ['class' => 'table-responsive'],
    'columns' => [
        [
            'format' => 'raw',
            'value' => function () {
                return Html::tag('i', '', ['class' => 'fa fa-bars drag-sort']);
            },
            'contentOptions' => ['class' => 'col--drag-sort']
        ],

        [
            'attribute' => 'id',
            'contentOptions' => ['style' => 'width: 60px; text-align: center;']
        ],
        [
            'attribute' => 'title',
            'format' => 'raw',
            'value' => function ($data) {
                /**
                 * @var \app\models\Works $data
                 */
                return Html::img($data->getThumbUploadUrl('thumb', 'preview'), [
                        'class' => 'img-circle img-preview'
                    ]) . ' ' . $data->title;
            }
        ],
        [
            'attribute' => 'description',
            'format' => 'raw',
            'value' => 'shortDescription'
        ],
        'excerpt',
        'link',
        // 'thumb',
        // 'thumb_2x',
        // 'created_at',
        // 'updated_at',

        [
            'class' => ActionColumn::className(),
        ],
    ],
]); ?>
