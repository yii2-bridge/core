<?php
/**
 * @var \yii\web\View $this
 * @var array $repoData
 * @var string $currentVersion
 */
$this->title = Yii::t('bridge', 'Dashboard');

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-4">
        <h3>Updates</h3>
        <?php if ($repoData['tag_name'] > $currentVersion) : ?>
            <div class="alert alert-warning">
                <i class="fa fa-exclamation"></i>
                <?= Yii::t('bridge', 'Update your admin panel by running <code>{0}</code>', ['composer update naffiq/yii2-bridge']) ?>
            </div>
        <?php else: ?>
            <div class="alert alert-success">
                <i class="fa fa-star"></i>
                <?= Yii::t('bridge', 'You have latest release of <code>{0}</code>', ['naffiq/yii2-bridge']) ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-md-8">
        <h3>Latest release info</h3>
        <div class="well">
            <h2><?= $repoData['name'] ?></h2>
            <h4>Version — <?= $repoData['tag_name'] ?></h4>
            <p>
                <?= $repoData['body'] ?>
            </p>

            <div class="links">
                <a href="<?= $repoData['html_url'] ?>" class="btn btn-link"><i class="fa fa-download"></i> Get release</a>
                <a href="https://github.com/naffiq/yii2-bridge" class="btn btn-link"><i class="fa fa-github"></i> Visit repo</a>
            </div>
        </div>
    </div>
</div>
