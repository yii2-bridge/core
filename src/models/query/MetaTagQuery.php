<?php

namespace Bridge\Core\Models\Query;

/**
 * This is the ActiveQuery class for [[\Bridge\Core\Models\MetaTag]].
 *
 * @see \Bridge\Core\Models\MetaTag
 */
class MetaTagQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \Bridge\Core\Models\MetaTag[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \Bridge\Core\Models\MetaTag|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
