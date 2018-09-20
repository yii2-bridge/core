<?php

use yii\db\Migration;

/**
 * Class m180919_103846_update_index_meta_tag_translations_table
 */
class m180919_103846_update_index_meta_tag_translations_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // drops index for columns `lang` and `meta_tag_id`
        $this->dropIndex(
            'uq_meta_tag_translations-lang-meta_tag_id',
            'meta_tag_translations'
        );

        // creates index for columns `meta_tag_id` and `lang`
        $this->createIndex(
            'uq_meta_tag_translations-lang-meta_tag_id',
            'meta_tag_translations',
            ['meta_tag_id', 'lang'],
            'true'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops index for columns `meta_tag_id` and `lang`
        $this->dropIndex(
            'uq_meta_tag_translations-lang-meta_tag_id',
            'meta_tag_translations'
        );

        // creates index for columns `lang` and `meta_tag_id`
        $this->createIndex(
            'uq_meta_tag_translations-lang-meta_tag_id',
            'meta_tag_translations',
            ['lang', 'meta_tag_id'],
            'true'
        );
    }
}
