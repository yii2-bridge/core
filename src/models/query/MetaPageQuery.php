<?php

namespace Bridge\Core\Models\Query;

/**
 * This is the ActiveQuery class for [[\Bridge\Core\Models\MetaPage]].
 *
 * @see \Bridge\Core\Models\MetaPage
 */
class MetaPageQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \Bridge\Core\Models\MetaPage[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \Bridge\Core\Models\MetaPage|array|null
     */
    public function one($db = null)
    {
    return parent::one($db);
    }
}
