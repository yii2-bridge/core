<?php

namespace Bridge\Core\Models\Query;

/**
 * This is the ActiveQuery class for [[\Bridge\Core\Models\MetaTagTranslation]].
 *
 * @see \Bridge\Core\Models\MetaTagTranslation
 */
class MetaTagTranslationQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \Bridge\Core\Models\MetaTagTranslation[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \Bridge\Core\Models\MetaTagTranslation|array|null
     */
    public function one($db = null)
    {
    return parent::one($db);
    }
}
