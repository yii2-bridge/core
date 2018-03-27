<?php

namespace naffiq\bridge\controllers;
use naffiq\bridge\models\Settings;
use naffiq\bridge\models\SettingsGroup;
use yii\helpers\ArrayHelper;

/**
 * SettingsController implements the CRUD actions for [[app\modules\admin\models\Settings]] model.
 * @see \naffiq\bridge\models\Settings
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

    /**
     * @inheritdoc
     */
    public function accessRules()
    {
        return [
            [
                'allow' => true,
                'roles' => ['admin'],
            ],
        ];
    }

    /**
     * @inheritdoc
     *
     * Remove pre-defined `index` action
     *
     */
    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index']);

        $actions['update']['returnUrl'] = function () {
            $settingId = \Yii::$app->request->get('id', false);
            $groupId = 'misc';
            if ($settingId) {
                $groupId = Settings::findOne($settingId)->group_id;
            }

            return [
                'index', 'group' => $groupId ? $groupId : 'misc'
            ];
        };

        return $actions;
    }


    public function actionIndex($group = null)
    {
        $settingsGroups = SettingsGroup::find()->all();
        $group = $group !== null ? $group : ArrayHelper::getValue($settingsGroups, '0.id', 'misc');

        return $this->render('index', ['settingsGroups' => $settingsGroups, 'group' => $group]);
    }
}
