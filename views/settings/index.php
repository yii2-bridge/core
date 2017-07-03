<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii2tech\admin\grid\ActionColumn;
use app\modules\admin\models\Settings;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\search\Settings */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Settings';
$this->params['breadcrumbs'][] = $this->title;
$this->params['contextMenuItems'] = [
    ['create']
];
?>

<div class="pull-right">
    <a href="<?= Url::to(['create']) ?>" class="btn btn-success"><i class="fa fa-plus"></i> Create</a>
</div>

<h1><?= $this->title ?></h1>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        'title',
        [
            'attribute' => 'value',
            'format' => 'raw',
            'value' => function ($data) {
                /**
                 * @var \app\modules\admin\models\Settings $data
                 */
                if ($data->type == Settings::TYPE_IMAGE) {
                    return Html::img($data->getThumbUploadUrl('value', 'preview'), [
                        'class' => 'img-circle img-preview'
                    ]);
                }
                return StringHelper::truncate(strip_tags($data->value), 150);
            }
        ],
        // 'type_settings:ntext',

        [
            'class' => ActionColumn::className(),
            'template' => '{update}'
        ],
    ],
]); ?>
