<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use naffiq\bridge\assets\AdminAsset;

AdminAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="main-nav">
    <div class="nav-bar">

        <nav class="navbar navbar-default" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-8">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <?= Breadcrumbs::widget([
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                        'options' => ['class' => 'breadcrumb breadcrumb-arrow'],
                        'activeItemTemplate' => "<li class=\"active\"><span>{link}</span></li>\n",
                        'homeLink' => [
                            'label' => \Yii::t('bridge', 'Home'),
                            'url' => ['/admin/']
                        ]
                    ]) ?>
                </div>
            </div>
        </nav>
    </div>
    <div class="nav-menu">
        <div class="hamburger">
            &beta;
        </div>

    </div>
</div>

<div class="wrap">

    <div class="container-fluid">
        <?= $content ?>
    </div>

    <footer class="footer">
        <div class="container-fluid">
            <div class="footer-copyright text-center">
                &beta;ridge  © <?= date('Y') ?>
                by <a href="https://github.com/naffiq" target="_blank">naffiq</a>
            </div>
        </div>
    </footer>
</div>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
