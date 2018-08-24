# Yii2 Bridge [![Build Status](https://travis-ci.org/naffiq/yii2-bridge.svg?branch=master)](https://travis-ci.org/naffiq/yii2-bridge) [![Maintainability](https://api.codeclimate.com/v1/badges/179010503a3350d57f81/maintainability)](https://codeclimate.com/github/naffiq/yii2-bridge/maintainability)

![Yet another admin panel](https://raw.githubusercontent.com/yii2-bridge/core/master/src/assets/dist/bridge.jpg)

### [Краткое введение и туториал на русском](http://telegra.ph/Yii2-Bridge-03-26)

Bridge delivers you simple content management system that utilizes best production-tested
components and plugins for Yii2.

## Installation

Add it as Composer dependency by running
```bash
$ composer require yii2-bridge/core
```

####  Be sure to configure module (instructions below), before running migrations.

## Configuration

Add module declaration to your config file for web config:
```php
<?php

return [
    // ... your config
    'modules' => [
        'admin' => [
            'class' => '\Bridge\Core\BridgeModule',
            // Add your projects modules here to keep right routing.
            'modules' => [
                'customModule' => ['class' => '\app\modules\your\CustomModule']
            ],
            // Add menu item of your content management module to menu
            'menu' => [
                [
                    'title' => 'Content',
                    'url' => ['/admin/content/default/index'],
                    'active' => ['module' => 'content'],
                    'icon' => 'list'
                ]
            ],
            // Alternatively you can define different menu items for different
            // roles. In that case it will override default menu items, such as
            // settings, users and dashboard
            'composeMenu' => function ($user, $roles, $authManager) {
                 /**
                  * @var \yii\web\User $user 
                  * @var \Da\User\Model\Role[] $roles
                  * @var \Da\User\Component\AuthDbManagerComponent $authManager 
                  */
                 if (isset($roles['admin'])) {
                     return require __DIR__ . '/menu-admin.php';
                 }
                 if ($user->can('editor')) {
                     return require __DIR__ . '/menu-editor.php';
                 }
                 if (in_array($user->id, $authManager->getUserIdsByRole('manager'))) {
                     return require __DIR__ . '/menu-manager.php';
                 }
                 // Or any other available method
                 
                 return __DIR__ . '/menu-default.php';
            }
        ]
    ],
    'bootstrap' => [        
        'admin' // add module id to bootstrap for proper aliases and url routes binding
    ]
];

```

And for console config, in order to run migrations:

```php
<?php
return [
    // ... your config
    'modules' => [
        'admin' => ['class' => '\Bridge\Core\BridgeModule']
    ],
    'bootstrap' => [        
        'admin' // add module id to bootstrap for proper aliases and url routes binding
    ]
]; 

```


## Setup

After installing and config setup (including database), you should have installation
executable in your vendor folder. You can run all the migrations required with single 
command:

```bash
$ ./vendor/bin/bridge-install
```

> Warning! This command is running with `--interactive=0` flag, which means it will not ask
confirmation for it.

## Usage

After running every step above you should have your admin panel running on `/admin` route.
The only thing left is to run command to create users. 

### Creating first user

Run following command to generate users:
```bash
$ php yii user/create EMAIL USERNAME PASSWORD ROLE 
```

So the correct command to create user with admin role for admin panel would be:
```bash
$ php yii user/create admin@sitename.kz admin PASSWORD admin
``` 

## Gii

Gii that is provided with bridge is packed with some improvements to basic gii.
When generating model with db fields ending by `image` or `file`, it would
automatically add corresponding upload behavior.
You can turn this behaviors off by clicking on checkbox in generator interface.

And also it has `Bridge CRUD generator`, which will generate necessary fields 
inputs and display it nicely to the index table.  

## Development and testing
Configure your `.env` (refer to `.env.example`). Run migrations with:
```bash
$ ./bin/bridge-install-dev
```

Bridge comes with console app for development and testing purposes, located in `bin` folder.
You can simply execute it with
```bash
$ php bin/yii.php CONTROLLER/ACTION [params]
``` 

Test package with
```bash
$ ./vendor/bin/phpunit
```

