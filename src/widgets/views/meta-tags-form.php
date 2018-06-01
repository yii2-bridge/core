<?php

/**
 * @var \yii\web\View $this
 * @var string $viewName
 * @var array $languages
 */
?>
<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingTwo">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                   href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    <?= Yii::t('bridge', 'Meta-tags') ?>
                </a>
            </h4>
        </div>
        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
            <div class="panel-body">
                <ul class="nav nav-tabs" role="tablist">

                    <?php foreach ($languages as $code => $label): ?>
                        <li role="presentation" class="<?= $code === \Yii::$app->language ? 'active' : '' ?>">
                            <a href="#translate-<?= $model::tableName() ?>-<?= $code ?>"
                               aria-controls="translate-<?= $model::tableName() ?>-<?= $code ?>" role="tab"
                               data-toggle="tab">
                                <?= $label ?>
                            </a>
                        </li>
                    <?php endforeach ?>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">

                    <?php foreach ($languages as $code => $label): ?>
                        <div role="tabpanel" class="tab-pane<?= $code === \Yii::$app->language ? ' active' : '' ?>"
                             id="translate-<?= $model::tableName() ?>-<?= $code ?>">

                            <?= $this->render($viewName, [
                                'languageCode' => $code,
                                'model' => $model,
                                'form' => $form
                            ]) ?>

                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
    </div>
</div>

