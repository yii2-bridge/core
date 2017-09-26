<?php
use yii\helpers\Url;
use naffiq\bridge\widgets\SideMenu;

/**
 * @var array $items
 * @var \yii\web\View $this
 */
?>


<ul class="side-menu" id="bridge--side-menu">
    <?php foreach ($items as $key => $item): ?>
        <li class="side-menu--item <?= SideMenu::isActive($item) ? 'active' : '' ?>"
            data-toggle="tooltip" data-placement="right" title="<?= $item['title'] ?>"
        >
            <?php if (!empty($item['url'])) : ?>
                <?= $this->render('_site-menu--item', ['item' => $item]); ?>
            <?php elseif(!empty($item['items'])) : ?>
                <a data-toggle="collapse" data-parent="#bridge--side-menu" href="#collapse-<?= $key ?>" aria-expanded="true" aria-controls="collapse<?= $key ?>" class="side-menu--link">
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
                <ul class="sub-menu panel-collapse collapse<?= SideMenu::isActive($item) ? ' in' : '' ?>" id="collapse-<?= $key ?>">
                    <?php foreach($item['items'] as $subItem): ?>
                        <li class="side-menu--item">
                            <?= $this->render('_site-menu--item', ['item' => $subItem]) ?>
                        </li>
                    <?php endforeach ?>
                </ul>
            <?php endif; ?>



        </li>
    <?php endforeach ?>
</ul>
