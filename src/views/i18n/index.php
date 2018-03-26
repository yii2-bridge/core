<?php
/**
 * @var View $this
 * @var SourceMessageSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 */

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use Zelenin\yii\modules\I18n\models\search\SourceMessageSearch;
use Zelenin\yii\modules\I18n\Module;

$this->title = Module::t('Translations');
$this->params['breadcrumbs'] = [
    $this->title
];
?>
<div class="message-index">
    <?= GridView::widget([
        'filterModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'id',
                'value' => function ($model, $index, $dataColumn) {
                    return $model->id;
                },
                'filter' => false
            ],
            [
                'attribute' => 'message',
                'format' => 'raw',
                'value' => function ($model, $index, $widget) {
                    return Html::a(Yii::t($model->category, $model->message), ['update', 'id' => $model->id], ['data' => ['pjax' => 0]]);
                }
            ],
            [
                'attribute' => 'category',
                'value' => function ($model, $index, $dataColumn) {
                    return $model->category;
                },
                'filter' => ArrayHelper::map($searchModel::getCategories(), 'category', 'category')
            ],
            [
                'attribute' => 'status',
                'value' => function ($model, $index, $widget) {
                    return '';
                },
                'filter' => $searchModel->getStatus()
            ]
        ]
    ]);
    ?>
</div>
