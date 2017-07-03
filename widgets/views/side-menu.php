<?php
use yii\helpers\Url;
use naffiq\bridge\widgets\SideMenu;

/**
 * @var array $items
 */
?>


<ul class="side-menu">
    <?php foreach ($items as $item): ?>
        <li class="side-menu--item <?= SideMenu::isActive($item) ? 'active' : '' ?>"
            data-toggle="tooltip" data-placement="right" title="<?= $item['title'] ?>"
        >
            <a href="<?= Url::to($item['url']) ?>">
                <?php if (!empty($item['image'])) : ?>
                    <img src="<?= $item['image'] ?>" alt="" class="img-circle" width="30">
                <?php else: ?>
                    <i class="fa fa-<?= $item['icon'] ?>"></i>
                <?php endif; ?>
            </a>
        </li>
    <?php endforeach ?>
</ul>
