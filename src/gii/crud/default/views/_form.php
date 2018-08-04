<?php

use Bridge\Core\Gii\Helpers\ColumnHelper;

/* @var $this yii\web\View */
/* @var $generator \Bridge\Core\Gii\CRUD\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$model->setScenario($generator->createScenario);
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\bootstrap\Html;
use Bridge\Core\Widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form Bridge\Core\Widgets\ActiveForm */
?>

<?= "<?php " ?>$form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-8">

<?php foreach ($generator->getColumnNames() as $attribute) {
    if (in_array($attribute, $safeAttributes) && $attribute !== 'id' && !($generator->generatePositionColumn && $attribute == 'position')) {
        echo ColumnHelper::pushTab(2) .  "<?= " . $generator->generateActiveField($attribute) . " ?>\n\n";
    }
} ?>

    </div>

    <div class="col-md-4">

    </div>
</div>

    <div class="form-group">
        <?= "<?= " ?>Html::submitButton($model->isNewRecord ? <?= $generator->generateString('Create') ?> : <?= $generator->generateString('Update') ?>, ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
<?= "<?php " ?>ActiveForm::end(); ?>
