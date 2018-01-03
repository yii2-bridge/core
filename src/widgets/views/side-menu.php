<?php

use yii\helpers\Url;
use naffiq\bridge\widgets\SideMenu;

/**
 * @var array $items
 * @var \yii\web\View $this
 */
?>

<ul class="side-menu" id="bridge--side-menu" role="tablist" aria-multiselectable="false">
    <?php foreach ($items as $key => $item): ?>
        <?php if(!SideMenu::isVisible($item)) continue ?>
        <li class="side-menu--item<?= SideMenu::isActive($item) ? ' active' : '' ?><?= !empty($item['items']) ? ' with--sub-menu' : '' ?>"
            data-toggle="tooltip" data-placement="right" title="<?= $item['title'] ?>"
        <?php if (!empty($item['items'])) : ?>
            role="tab" id="heading<?= $key ?>"
        <?php endif; ?>
        >
            <?php if (!empty($item['url'])) : ?>
                <?= $this->render('_site-menu--item', ['item' => $item]); ?>
            <?php elseif (!empty($item['items'])) : ?>

                <a data-toggle="collapse" data-parent="#bridge--side-menu"
                   aria-expanded="false" aria-controls="collapse-<?= $key ?>"
                   href="#collapse-<?= $key ?>" class="side-menu--link side-menu--collapsable"
                >
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
                <ul class="sub-menu collapse<?= SideMenu::isActive($item) ? ' in' : '' ?>"
                    id="collapse-<?= $key ?>" role="tabpanel" aria-labelledby="heading<?= $key ?>">
                    <?php foreach ($item['items'] as $subItem): ?>
                        <?php if(!SideMenu::isVisible($subItem)) continue ?>
                        <li class="side-menu--item">
                            <?= $this->render('_site-menu--item', ['item' => $subItem]) ?>
                        </li>
                    <?php endforeach ?>
                </ul>

            <?php endif; ?>

        </li>
    <?php endforeach ?>
</ul>
