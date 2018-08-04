<?php
/**
 * @var View $this
 * @var SourceMessage $model
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;
use Zelenin\yii\modules\I18n\models\SourceMessage;
use Zelenin\yii\modules\I18n\Module;
use Bridge\Core\Widgets\ActiveForm;

/** @var \naffiq\bridge\BridgeModule $adminModule */
$adminModule = \Yii::$app->getModule('admin');

$this->title = Module::t('Update') . ': ' . $model->message;
$this->params['breadcrumbs'] = [
    ['label' => Module::t('Translations'), 'url' => ['index']],
    ['label' => $this->title]
];
?>
<div class="message-update">
    <div class="message-form">
        <?php $form = ActiveForm::begin(); ?>
        <div class="field">
            <div class="row">
                <?php foreach ($model->messages as $language => $message) : ?>
                    <div class="col-md-3">
                        <?= $form->field($model->messages[$language], '[' . $language . ']translation')
                            ->label(ArrayHelper::getValue($adminModule->getLanguagesList(), $language)) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?= Html::submitButton(Module::t('Update'), ['class' => 'btn btn-success']) ?>
        <?php $form::end(); ?>
    </div>
</div>
