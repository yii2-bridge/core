<?php

namespace naffiq\bridge\models\query;

/**
 * This is the ActiveQuery class for [[\naffiq\bridge\models\MetaTagTranslation]].
 *
 * @see \naffiq\bridge\models\MetaTagTranslation
 */
class MetaTagTranslationQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \naffiq\bridge\models\MetaTagTranslation[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \naffiq\bridge\models\MetaTagTranslation|array|null
     */
    public function one($db = null)
    {
    return parent::one($db);
    }
}
