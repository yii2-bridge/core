<?php

namespace naffiq\bridge\controllers;

use Da\User\Controller\AdminController;

/**
 * UsersController implements the CRUD actions for [[app\models\Users]] model.
 * @see \naffiq\bridge\models\Users
 */
class UsersController extends AdminController
{
    /**
     * @inheritdoc
     */
    public $layout = '@bridge/views/layouts/main';

    /**
     * @inheritdoc
     */
    public $modelClass = 'naffiq\bridge\models\Users';
    /**
     * @inheritdoc
     */
    public $searchModelClass = 'naffiq\bridge\models\search\UsersSearch';

    /**
     * @var string
     */
    public $updateScenario = 'update';

    /**
     * @var string
     */
    public $createScenario = 'create';
}
