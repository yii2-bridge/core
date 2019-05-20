<?php

use yii\db\Migration;

/**
 * Handles the creation of table `meta_tag_translations`.
 */
class m180424_121053_create_meta_tag_translations_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('meta_tag_translations', [
            'id' => $this->primaryKey(),
            'lang' => $this->string()->notNull()->comment('Translation language'),
            'meta_tag_id' => $this->integer()->notNull()->comment('Meta tag'),
            'title' => $this->string()->comment('Headline'),
            'description' => $this->text()->comment('Description'),
            'keywords' => $this->string()->comment('Keywords'),
            'image' => $this->string()->comment('Picture'),
            'type' => $this->string()->comment('Page type'),
        ]);

        // creates index for columns `lang` and `meta_tag_id`
        $this->createIndex(
            'uq_meta_tag_translations-lang-meta_tag_id',
            'meta_tag_translations',
            ['lang', 'meta_tag_id'],
            'true'
        );

        // add foreign key for table `meta_tags`
        $this->addForeignKey(
            'fk_meta_tag_translations-meta_tag_id',
            'meta_tag_translations',
            'meta_tag_id',
            'meta_tags',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `meta_tags`
        $this->dropForeignKey(
            'fk_meta_tag_translations-meta_tag_id',
            'meta_tag_translations'
        );

        // drops index for columns `lang` and `meta_tag_id`
        $this->dropIndex(
            'uq_meta_tag_translations-lang-meta_tag_id',
            'meta_tag_translations'
        );

        $this->dropTable('meta_tag_translations');
    }
}
