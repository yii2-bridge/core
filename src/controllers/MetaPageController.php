<?php

namespace naffiq\bridge\controllers;

use yii\helpers\ArrayHelper;
use yii2tech\admin\actions\Position;
use dosamigos\grid\actions\ToggleAction;

/**
 * MetaPageController implements the CRUD actions for [[naffiq\bridge\models\MetaPage]] model.
 * @see \naffiq\bridge\models\MetaPage
 */
class MetaPageController extends BaseAdminController
{
    /**
     * @inheritdoc
     */
    public $modelClass = 'naffiq\bridge\models\MetaPage';
    /**
     * @inheritdoc
     */
    public $searchModelClass = 'naffiq\bridge\models\search\MetaPageSearch';


    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = ArrayHelper::merge(
            parent::actions(),
            [
                'toggle' => [
                    'class' => ToggleAction::className(),
                    'modelClass' => 'naffiq\bridge\models\MetaPage',
                    'onValue' => 1,
                    'offValue' => 0
                ],
                'position' => [
                    'class' => Position::className(),
                ],
            ]
        );

        unset($actions['create']);
        unset($actions['delete']);
        unset($actions['position']);

        return $actions;
    }
}
