<?php

use dosamigos\grid\GridView;
use yii\helpers\Html;
use yii2tech\admin\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $searchModel Bridge\Core\Models\Search\MetaPageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('bridge', 'Meta-tags');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['class' => 'grid-view table-responsive'],
    'behaviors' => [
        \dosamigos\grid\behaviors\ResizableColumnsBehavior::className()
    ],
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'class' => '\Bridge\Core\Widgets\Columns\TitledImageColumn',
            'imageAttribute' => 'metaTag.translation.image',
            'attribute' => 'title',
            'label' => $searchModel->getAttributeLabel('title'),
            'value' => 'metaTag.translation.title',
        ],
        'module',
        'controller',
        'action',
        [
            'class' => ActionColumn::className(),
            'buttons' => [
                'delete' => [
                    'visible' => false
                ],
            ]
        ],
    ],
]); ?>
