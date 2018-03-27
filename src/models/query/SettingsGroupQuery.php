<?php

namespace naffiq\bridge\models\query;

/**
 * This is the ActiveQuery class for [[\app\models\SettingsGroup]].
 *
 * @see \naffiq\bridge\models\SettingsGroup
 */
class SettingsGroupQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \naffiq\bridge\models\SettingsGroup[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \naffiq\bridge\models\SettingsGroup|array|null
     */
    public function one($db = null)
    {
    return parent::one($db);
    }
}
