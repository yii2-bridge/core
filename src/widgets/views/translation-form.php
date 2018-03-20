<?php

use yii\helpers\ArrayHelper;

/**
 * @var \yii\web\View $this
 * @var string $viewName
 * @var array $languages
 */
?>

<ul class="nav nav-tabs" role="tablist">

    <?php foreach ($languages as $code => $label): ?>
        <li role="presentation" class="<?= $code === \Yii::$app->language ? 'active' : ''?>">
            <a href="#translate-<?= $code ?>" aria-controls="translate-<?= $code ?>" role="tab" data-toggle="tab">
                <?= $label ?>
            </a>
        </li>
    <?php endforeach ?>
</ul>

<!-- Tab panes -->
<div class="tab-content">

    <?php foreach ($languages as $code => $label): ?>
        <div role="tabpanel" class="tab-pane<?= $code === \Yii::$app->language ? ' active' : ''?>" id="translate-<?= $code ?>">

            <?= $this->render($viewName, [
                'languageCode' => $code,
                'model' => $model,
                'form' => $form
            ]) ?>

        </div>
    <?php endforeach ?>
</div>