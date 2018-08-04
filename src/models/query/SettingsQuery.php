<?php

namespace Bridge\Core\Models\Query;

/**
 * This is the ActiveQuery class for [[\Bridge\Core\Models\Settings]].
 *
 * @see \Bridge\Core\Models\Settings
 */
class SettingsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \Bridge\Core\Models\Settings[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \Bridge\Core\Models\Settings|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * Search by key
     *
     * @param string $key
     * @return SettingsQuery
     */
    public function key($key)
    {
        return $this->andWhere(['key' => $key]);
    }

    /**
     * Used for mapping Settings to memory
     *
     * @return SettingsQuery
     */
    public function keyValueMap()
    {
        return $this->select(['key', 'value'])->asArray();
    }
}
