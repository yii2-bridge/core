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
    'components' => [
        'urlManager' => [
            'routes' => [
                'admin/<controller>/<action>' => 'admin/<controller>/<action>', 
                'admin/<module>/<controller>/<action>' => 'admin/<module>/<controller>/<action>' 
            ]            
        ]
    ],
    'modules' => [
        'admin' => [
            'class' => '\naffiq\bridge\BridgeModule'
        ]
    ]
];

```
