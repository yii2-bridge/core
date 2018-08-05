<?php

namespace Bridge\Core\Models\Query;

/**
 * This is the ActiveQuery class for [[\app\models\SettingsGroup]].
 *
 * @see \Bridge\Core\Models\SettingsGroup
 */
class SettingsGroupQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \Bridge\Core\Models\SettingsGroup[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \Bridge\Core\Models\SettingsGroup|array|null
     */
    public function one($db = null)
    {
    return parent::one($db);
    }
}
