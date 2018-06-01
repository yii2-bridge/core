<?php

use dosamigos\grid\GridView;
use yii\helpers\Html;
use yii2tech\admin\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $searchModel naffiq\bridge\models\search\MetaPageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('bridge', 'Meta Pages');
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

        /*'id',
        'meta_tag_id',*/
        // TODO: Добавить фильтрацию по заголовкам
        [
            'class' => 'naffiq\bridge\widgets\columns\TitledImageColumn',
            'imageAttribute' => 'metaTag.translation.image',
            'attribute' => 'title',
//            'label' => $translation->getAttributeLabel('title'),
            'value' => 'metaTag.translation.title',
//            'filter' => Html::activeInput('text', $searchModel, 'title', ['placeholder' => $translation->getAttributeLabel('title'), 'class' => 'form-control']),
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
