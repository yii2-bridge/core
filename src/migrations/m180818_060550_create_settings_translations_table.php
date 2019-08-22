<?php

use yii\db\Migration;

/**
 * Handles the creation of table `settings_translations`.
 */
class m180818_060550_create_settings_translations_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('settings_translations', [
            'id' => $this->primaryKey(),
            'lang' => $this->string()->notNull()->comment('Translation language'),
            'settings_id' => $this->integer()->notNull()->comment('Customization'),
            'value' => $this->text()->null()->comment('Value'),
        ]);

        // creates index for columns `settings_id` and `lang`
        $this->createIndex(
            'uq_settings_translations-settings_id-lang',
            'settings_translations',
            ['settings_id', 'lang'],
            'true'
        );

        // creates index for column `settings_id`
        $this->createIndex('idx_settings_id', 'settings_translations', 'settings_id');

        // add foreign key for table `settings`
        $this->addForeignKey(
            'fk_settings_translations-settings_id',
            'settings_translations',
            'settings_id',
            'settings',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops index for column `settings_id`
        $this->dropIndex(
            'idx_settings_id',
            'settings_translations'
        );

        // drops foreign key for table `settings_translations`
        $this->dropForeignKey(
            'fk_settings_translations-settings_id',
            'settings_translations'
        );

        // drops index for columns `lang` and `meta_tag_id`
        $this->dropIndex(
            'uq_settings_translations-lang-settings_id',
            'settings_translations'
        );

        $this->dropTable('settings_translations');
    }
}
