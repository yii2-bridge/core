<?php

namespace Bridge\Core\Models\Query;

/**
 * This is the ActiveQuery class for [[\Bridge\Core\Models\MetaModel]].
 *
 * @see \Bridge\Core\Models\MetaModel
 */
class MetaModelQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \Bridge\Core\Models\MetaModel[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \Bridge\Core\Models\MetaModel|array|null
     */
    public function one($db = null)
    {
    return parent::one($db);
    }
}
