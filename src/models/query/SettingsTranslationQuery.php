<?php

namespace Bridge\Core\Models\Query;

/**
 * This is the ActiveQuery class for [[\Bridge\Core\Models\SettingsTranslation]].
 *
 * @see \Bridge\Core\Models\SettingsTranslation
 */
class SettingsTranslationQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return \Bridge\Core\Models\SettingsTranslation[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \Bridge\Core\Models\SettingsTranslation|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
