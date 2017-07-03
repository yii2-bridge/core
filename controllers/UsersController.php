<?php

namespace naffiq\bridge\controllers;

/**
 * UsersController implements the CRUD actions for [[app\models\Users]] model.
 * @see \naffiq\bridge\models\Users
 */
class UsersController extends BaseAdminController
{
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
