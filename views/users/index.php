<?php

use yii\grid\GridView;
use yii2tech\admin\grid\ActionColumn;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\search\UsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
$this->params['contextMenuItems'] = [
    ['create']
];
?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'tableOptions' => ['class' => 'table table-striped'],
    'options' => ['class' => 'table-responsive'],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'id',
            'contentOptions' => ['style' => 'width: 60px; text-align: center;']
        ],
        [
            'attribute' => 'username',
            'format' => 'raw',
            'value' => function ($data) {
                /**
                 * @var \app\models\Users $data
                 */
                return Html::img($data->getThumbUploadUrl('avatar', 'preview'), [
                        'class' => 'img-circle img-preview'
                    ]) . ' ' . $data->username;
            }
        ],
        // 'is_active',
        // 'created_at',
        // 'updated_at',

        [
            'class' => ActionColumn::className(),
        ],
    ],
]); ?>
