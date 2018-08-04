<?php

/* @var $this \yii\web\View */

/* @var $content string */

use Bridge\Core\Assets\AdminAsset;
use naffiq\bridge\models\Settings;
use naffiq\bridge\widgets\SideMenu;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use yii2tech\admin\widgets\ButtonContextMenu;

AdminAsset::register($this);

/** @var $user \Da\User\Model\User */
$user = \Yii::$app->user->identity;
/** @var \naffiq\bridge\BridgeModule $adminModule */
$adminModule = \Yii::$app->getModule('admin');

$isMenuWide = \Yii::$app->session->get('bridge-menu-state', 0);
\naffiq\bridge\widgets\Toastr::registerToasts($this);
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
<body data-menu-toggle-url="<?= Url::to(['/admin/default/set-menu-state']) ?>?language=<?= \Yii::$app->language ?>">
<?php $this->beginBody() ?>

<div class="nav-menu<?= $isMenuWide ? ' wide' : '' ?>">
    <div class="nav-menu--header">
        <div class="nav-menu--header-hamburger">
            <i class="fa fa-arrow-left"></i>
        </div>
        <div class="nav-menu--header-title">
            <?= Settings::group('admin', [
                'icon' => 'fa-wrench',
                'title' => 'Admin',
            ])->getOrCreate('admin-header', [
                'title' => 'Admin title',
                'value' => 'Bridge',
                'type' => Settings::TYPE_STRING
            ]) ?>
        </div>
    </div>


    <?= SideMenu::widget([
        'items' => $adminModule->getMenuItems()
    ]) ?>

    <?php if (!\Yii::$app->user->isGuest) : ?>
        <?php ActiveForm::begin(['action' => ['/admin/default/logout'], 'options' => [
            'class' => 'form--sign-out'
        ]]) ?>
        <button type="submit" class="btn btn-sign-out" data-toggle="tooltip" data-placement="right"
                title="<?= Yii::t('bridge', 'Sign&nbsp;out') ?>">
            <span class="sign-out--title">
                <?= Yii::t('bridge', 'Logout') ?>
            </span> <i class="fa fa-sign-out"></i>
        </button>
        <?php ActiveForm::end(); ?>
    <?php endif; ?>
</div>

<div class="bridge-wrap<?= $isMenuWide ? ' nav-wide' : '' ?>">
    <div class="content-header container-fluid">
        <?php if ($adminModule->showLanguageSwitcher) : ?>
            <div class="dropdown" style="margin-right: 15px;">
                <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                        class="btn btn-default">
                    <i class="fa fa-globe"></i> <?= ArrayHelper::getValue($adminModule->getLanguagesList(), \Yii::$app->language, $adminModule->defaultLanguage) ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dLabel" style="min-width: 50px">
                    <?php foreach (\Yii::$app->urlManager->languages as $label => $code): ?>
                        <li><a href="<?= Url::to(ArrayHelper::merge(['', 'language' => $code], \Yii::$app->request->get())) ?>"><?= ArrayHelper::getValue($adminModule->languages, $code, $label) ?></a></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif; ?>

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

    <div class="container-fluid clearfix">


        <div class="row bridge-admin--page-title-row">
            <div class="col-md-7 col-xs-6">
                <h1 class="bridge-admin--page-title"><?= Html::encode(isset($this->params['header']) ? $this->params['header'] : $this->title) ?></h1>
            </div>
            <div class="col-md-5 text-right col-xs-6">
                <?= ButtonContextMenu::widget([
                    'items' => isset($this->params['contextMenuItems']) ? $this->params['contextMenuItems'] : []
                ]) ?>
            </div>
        </div>

        <?php if (!ArrayHelper::getValue($this->params, 'no-panel', false)) : ?>
            <div class="panel">
                <div class="panel-body">
                    <?= $content ?>
                </div>
            </div>
        <?php else: ?>
            <?= $content ?>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <div class="container-fluid">
            <div class="footer-copyright text-center">
                <?= Settings::group('admin')->getOrCreate('footer-copyright', [
                    'title' => 'Footer-copyright',
                    'value' => '&beta;ridge © ' . date('Y') .
                ' by <a href="https://github.com/naffiq" target="_blank">naffiq</a>',
                    'type' => Settings::TYPE_TEXT
                ]) ?>
            </div>
        </div>
    </footer>
</div>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
