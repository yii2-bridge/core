<?php

/* @var $this \yii\web\View */
/* @var $content string */

use naffiq\bridge\assets\AdminAsset;
use naffiq\bridge\widgets\SideMenu;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use yii2tech\admin\widgets\ButtonContextMenu;

AdminAsset::register($this);

/**
 * @var $user \naffiq\bridge\models\Users
 */
$user = \Yii::$app->user->identity;
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

<div class="main-nav clearfix">
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
                            'label' => 'Home',
                            'url' => ['/admin/']
                        ]
                    ]) ?>
                </div>
                <div class="collapse navbar-collapse pull-right" id="bs-example-navbar-collapse-8">
                </div>
            </div>
        </nav>
    </div>
    <div class="nav-menu">
        <div class="hamburger">
            &beta;
        </div>

        <?= SideMenu::widget([
            'items' => ArrayHelper::merge([
                [
                    'title' => 'Profile',
                    'url' => ['/admin/users/update', 'id' => $user->id],
                    'active' => ['controller' => 'default', 'action' => 'update'],
//                    'image' => $user->getThumbUploadUrl('avatar', 'preview'),
                    'icon' => 'user'
                ],
                [
                    'title' => 'Dashboard',
                    'url' => ['/admin/default/index'],
                    'active' => ['controller' => 'default'],
                    'icon' => 'grav',
                ],
                [
                    'title' => 'Settings',
                    'url' => ['/admin/settings/index'],
                    'active' => ['controller' => 'settings'],
                    'icon' => 'gear'
                ],
                [
                    'title' => 'Users',
                    'url' => ['/admin/users/index'],
                    'active' => ['controller' => 'users'],
                    'icon' => 'users'
                ]
            ], empty($this->params['admin-menu']) ? [] : $this->params['admin-menu'])
        ]) ?>

        <?php ActiveForm::begin(['action' => ['/admin/default/logout'], 'options' => [
            'class' => 'form--sign-out'
        ]]) ?>
        <button type="submit" class="btn btn-sign-out" data-toggle="tooltip" data-placement="right" title="Sign&nbsp;out">
            <i class="fa fa-sign-out"></i>
        </button>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<div class="wrap ">

    <div class="container-fluid clearfix">
        <h1><?=  Html::encode(isset($this->params['header']) ? $this->params['header'] : $this->title) ?></h1>

        <p>
            <?=  ButtonContextMenu::widget([
                'items' => isset($this->params['contextMenuItems']) ? $this->params['contextMenuItems'] : []
            ]) ?>
        </p>

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
