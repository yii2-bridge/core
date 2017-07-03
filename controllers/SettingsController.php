<?php

namespace naffiq\bridge\controllers;

/**
 * SettingsController implements the CRUD actions for [[app\modules\admin\models\Settings]] model.
 * @see naffiq\bridge\models\Settings
 */
class SettingsController extends BaseAdminController
{
    /**
     * @inheritdoc
     */
    public $modelClass = 'naffiq\bridge\models\Settings';
    /**
     * @inheritdoc
     */
    public $searchModelClass = 'naffiq\bridge\models\search\SettingsSearch';

    /**
     * @var string
     */
    public $updateScenario = 'update';

    /**
     * @var string
     */
    public $createScenario = 'create';
}
