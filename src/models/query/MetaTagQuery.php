<?php

namespace naffiq\bridge\models\query;

/**
 * This is the ActiveQuery class for [[\naffiq\bridge\models\MetaTag]].
 *
 * @see \naffiq\bridge\models\MetaTag
 */
class MetaTagQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \naffiq\bridge\models\MetaTag[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \naffiq\bridge\models\MetaTag|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
