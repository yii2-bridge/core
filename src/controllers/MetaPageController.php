<?php

namespace Bridge\Core\Controllers;

use yii\helpers\ArrayHelper;
use yii2tech\admin\actions\Position;
use dosamigos\grid\actions\ToggleAction;

/**
 * MetaPageController implements the CRUD actions for [[Bridge\Core\Models\MetaPage]] model.
 * @see \Bridge\Core\Models\MetaPage
 */
class MetaPageController extends BaseAdminController
{
    /**
     * @inheritdoc
     */
    public $modelClass = 'Bridge\Core\Models\MetaPage';
    /**
     * @inheritdoc
     */
    public $searchModelClass = 'Bridge\Core\Models\Search\MetaPageSearch';


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
                    'modelClass' => 'Bridge\Core\Models\MetaPage',
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
