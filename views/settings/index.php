<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii2tech\admin\grid\ActionColumn;
use naffiq\bridge\models\Settings;

/* @var $this yii\web\View */
/* @var $searchModel naffiq\bridge\models\search\SettingsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Settings';
$this->params['breadcrumbs'][] = $this->title;
$this->params['contextMenuItems'] = [
    ['create']
];
?>

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
                 * @var \naffiq\bridge\models\Settings $data
                 */
                if ($data->type == Settings::TYPE_IMAGE) {
                    return Html::img($data->getThumbUploadUrl('value', 'preview'), [
                        'class' => 'img-circle img-preview'
                    ]);
                }
                return StringHelper::truncate(strip_tags($data->value), 150);
            }
        ],
        [
            'class' => ActionColumn::className(),
            'template' => '{update}'
        ],
    ],
]); ?>
