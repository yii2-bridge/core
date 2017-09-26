<?php

use yii\helpers\Url;

/**
 * @var array $item
 * @var \yii\web\View $this
 */
?>

<a href="<?= Url::to($item['url']) ?>" class="side-menu--link">
    <div class="icon">
        <?php if (!empty($item['image'])) : ?>
            <img src="<?= $item['image'] ?>" alt="" class="img-circle" width="30">
        <?php else: ?>
            <i class="fa fa-<?= $item['icon'] ?>"></i>
        <?php endif; ?>
    </div>
    <div class="title">
        <?= $item['title'] ?>
    </div>
</a>
