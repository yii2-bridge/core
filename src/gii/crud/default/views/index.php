<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use naffiq\bridge\gii\helpers\ColumnHelper;

/* @var $this yii\web\View */
/* @var $generator \naffiq\bridge\gii\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();
$contexts = $generator->getContexts();
/** @var \yii\db\ActiveRecord $model */
$model = new $generator->modelClass();

echo "<?php\n";
?>

<?php if ($generator->indexWidgetType === 'grid'): ?>
use dosamigos\grid\GridView;
use yii2tech\admin\grid\ActionColumn;
<?php else: ?>
use yii\widgets\ListView;
<?php endif ?>
<?php if ($generator->shouldSoftDelete()) : ?>
use yii\helpers\Html;
<?php endif; ?>

/* @var $this yii\web\View */
/* @var $searchModel <?= !empty($generator->searchModelClass) ? ltrim($generator->searchModelClass, '\\') : 'yii\base\Model' ?> */
/* @var $dataProvider yii\data\ActiveDataProvider */
<?php if (!empty($contexts)): ?>
/* @var $controller <?= $generator->controllerClass ?>|yii2tech\admin\behaviors\ContextModelControlBehavior */

$controller = $this->context;
$contextUrlParams = $controller->getContextQueryParams();
<?php endif ?>

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?><?=
    $generator->shouldSoftDelete() ? " . (\$searchModel->isDeleted ? ' — ' . \Yii::t('bridge', 'Trash') : '')" : ''
?>;
<?php if (!empty($contexts)): ?>
foreach ($controller->getContextModels() as $name => $contextModel) {
    $this->params['breadcrumbs'][] = ['label' => $name, 'url' => $controller->getContextUrl($name)];
    $this->params['breadcrumbs'][] = ['label' => $contextModel->id, 'url' => $controller->getContextModelUrl($name)];
}
<?php endif ?>
$this->params['breadcrumbs'][] = $this->title;
<?php if (!empty($contexts)): ?>
$this->params['contextMenuItems'] = [
    array_merge(['create'], $contextUrlParams)
];
<?php else: ?>
$this->params['contextMenuItems'] = [
<?php if ($generator->shouldSoftDelete()) : ?>
    [
    'url' => ['index', Html::getInputName($searchModel, 'isDeleted') => !$searchModel->isDeleted],
    'label' => $searchModel->isDeleted ? \Yii::t('bridge', 'All records') : \Yii::t('bridge', 'Trash'),
    'icon' => $searchModel->isDeleted ? 'share-alt' : 'trash',
    'class' => 'btn btn-' . ($searchModel->isDeleted ? 'soft-info' : 'trash'),
    ],
<?php endif; ?>
    ['create'],
];
<?php endif ?>
?>
<?php if (!empty($generator->searchModelClass) && $generator->indexWidgetType !== 'grid'): ?>
<?= "\n    <?php " ?>echo $this->render('_search', ['model' => $searchModel]); ?>
<?php endif; ?>

<?php if ($generator->indexWidgetType === 'grid'): ?>
<?= "<?= " ?>GridView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['class' => 'grid-view table-responsive'],
    'behaviors' => [
        \dosamigos\grid\behaviors\ResizableColumnsBehavior::className()
    ],
    <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n    'columns' => [\n" : "'columns' => [\n"; ?>
        ['class' => 'yii\grid\SerialColumn'],

<?php
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo ColumnHelper::pushTab(2) . "'" . $name . "',\n";
        } else {
            echo ColumnHelper::pushTab(2) . "// '" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        $format = $generator->generateGridColumnFormat($column);
        if ($format === false) continue;

        echo ColumnHelper::pushTab(2) . (++$count > 5 ? '// ' : '');
        if (is_array($format)) {
            echo "[\n";
            foreach ($format as $item => $value) {
                echo ColumnHelper::pushTab(3) . ($count > 5 ? '// ' : '') . "'{$item}' => " . (is_string($value) ? "'{$value}'" : $value) . ",\n";
            }
            echo ColumnHelper::pushTab(2) . ($count > 5 ? '// ' : '') . "],\n";
        } else {
            echo "'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>

        [
            'class' => ActionColumn::className(),
        ],
    ],
]); ?>
<?php else: ?>
<?= "<?= " ?>ListView::widget([
    'dataProvider' => $dataProvider,
    'itemOptions' => ['class' => 'item'],
    'itemView' => function ($model, $key, $index, $widget) {
        return Html::a(Html::encode($model-><?= $nameAttribute ?>), array_merge(['view', <?= $urlParams ?>], $contextUrlParams));
    },
]) ?>
<?php endif; ?>
