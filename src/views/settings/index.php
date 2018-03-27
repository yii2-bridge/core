<?php


use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $group string */
/* @var $settingsGroups \naffiq\bridge\models\SettingsGroup[] */

$this->title = Yii::t('bridge', 'Settings');
$this->params['breadcrumbs'][] = $this->title;
$this->params['contextMenuItems'] = [
    ['create'],
];

$this->params['no-panel'] = true;
?>

<div class="row">
    <div class="col-md-3">
        <ul class="nav nav-pills nav-stacked" role="tablist">
            <?php foreach ($settingsGroups as $settingsGroup): ?>
                <li role="presentation" class="<?= $group == $settingsGroup->id ? 'active' : '' ?>">
                    <a href="#group-<?= $settingsGroup->id ?>" aria-controls="group-<?= $settingsGroup->id ?>"
                       role="tab" data-toggle="tab">
                        <i class="fa <?= $settingsGroup->icon ?>"></i> <?= $settingsGroup->title ?>
                    </a>
                </li>
            <?php endforeach ?>
            <li role="presentation" class="<?= $group === 'misc' ? 'active' : '' ?>">
                <a href="#group-misc" aria-controls="group-misc" role="tab" data-toggle="tab">
                    <i class="fa fa-cogs"></i> <?= Yii::t('bridge', 'Miscellaneous') ?>
                </a>
            </li>

            <li>
                <hr>
            </li>

            <li class="add-settings-group">
                <a href="<?= Url::to(['/admin/settings-group/create']) ?>">
                    <i class="fa fa-plus"></i> <?= Yii::t('bridge', 'Add group') ?>
                </a>
            </li>
        </ul>

    </div>

    <div class="col-md-9">
        <div class="tab-content">
            <?php foreach ($settingsGroups as $settingsGroup): ?>
                <div role="tabpanel" class="tab-pane<?= $group == $settingsGroup->id ? ' active' : '' ?>" id="group-<?= $settingsGroup->id ?>">
                    <h3>
                        <i class="fa <?= $settingsGroup->icon ?>"></i> <?= $settingsGroup->title ?>

                        <div class="btn-group">
                        <a href="<?= Url::to(['/admin/settings-group/update', 'id' => $settingsGroup->id]) ?>" class="btn btn-xs btn-info">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a href="<?= Url::to(['/admin/settings-group/delete', 'id' => $settingsGroup->id]) ?>" class="btn btn-xs btn-danger" data-method="post" data-confirm="<?= Yii::t('bridge', 'Are you sure?') ?>">
                            <i class="fa fa-trash"></i>
                        </a>
                        </div>
                    </h3>

                    <?= \naffiq\bridge\widgets\SettingsGridView::widget(['group' => $settingsGroup]) ?>
                </div>
            <?php endforeach ?>


            <div role="tabpanel" class="tab-pane<?= $group == 'misc' ? ' active' : '' ?>" id="group-misc">
                <h3><i class="fa fa-cogs"></i> <?= \Yii::t('bridge', 'Miscellaneous') ?></h3>
                <?= \naffiq\bridge\widgets\SettingsGridView::widget() ?>
            </div>
        </div>
    </div>
</div>

