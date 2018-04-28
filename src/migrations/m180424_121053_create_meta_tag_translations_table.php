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
            'lang' => $this->string()->notNull()->comment('Язык перевода'),
            'meta_tag_id' => $this->integer()->notNull()->comment('Мета-тег'),
            'title' => $this->string()->comment('Заголовок'),
            'description' => $this->text()->comment('Описание'),
            'keywords' => $this->string()->comment('Ключевые слова'),
            'image' => $this->string()->comment('Изображение'),
            'type' => $this->string()->comment('Тип страницы'),
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
