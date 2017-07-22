# Yii2 Bridge

![Yet another admin panel](https://raw.githubusercontent.com/naffiq/yii2-bridge/master/assets/dist/bridge.jpg)

Bridge delivers you simple content management system that utilizes best production-tested
components and plugins for Yii2.

## Installation

Add it as Composer dependency by running
```bash
$ composer require naffiq/yii2-bridge
```

Run migrations from package by specifying `--migrationPath` such as follows:
```bash
$ php yii migrate --migrationPath=@vendor/naffiq/yii2-bridge/migrations
```

## Configuration

Add module declaration to your config file:
```php
<?php

return [
    'modules' => [
        'admin' => [
            'class' => '\naffiq\bridge\BridgeModule',
            // Add your content management module here.
            'modules' => [
                'content' => ['class' => '\app\modules\content\Module']
            ],
            // Add menu item of your content management module to menu
            'menu' => [
                [
                    'title' => 'Content',
                    'url' => ['/admin/content/default/index'],
                    'active' => ['module' => 'content'],
                    'icon' => 'list'
                ]
            ]
        ]
    ],
    'bootstrap' => [        
        'admin' // add module id to bootstrap for proper aliases and url routes binding
    ]
];

```
