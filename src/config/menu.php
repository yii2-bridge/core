<?php
return [
    [
        'title' => \Yii::t('bridge', 'Dashboard'),
        'url' => ['/admin/default/index'],
        'active' => ['module' => 'admin', 'controller' => 'default', 'action' => 'index'],
        'icon' => 'grav',
    ],
    \Yii::t('bridge', 'System'),
    [
        'title' => \Yii::t('bridge', 'Settings'),
        'url' => ['/admin/settings/index'],
        'active' => ['module' => 'admin', 'controller' => 'settings'],
        'icon' => 'gear',
        'isVisible' => ['admin']
    ],
    [
        'title' => \Yii::t('bridge', 'Users'),
        'url' => ['/user/admin/index'],
        'active' => ['module' => 'user'],
        'icon' => 'users',
        'isVisible' => ['admin']
    ],
    [
        'title' => \Yii::t('bridge', 'File manager'),
        'url' => ['/admin/default/elfinder'],
        'active' => ['module' => 'admin', 'controller' => 'default', 'action' => 'elfinder'],
        'icon' => 'file',
        'isVisible' => \Yii::$app->getModule('admin')->allowedRoles
    ],
    \Yii::t('bridge', 'Content'),
];