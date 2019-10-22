<?php

use yii\db\Migration;

/**
 * Handles the creation of table `meta_pages`.
 */
class m180424_121055_create_meta_pages_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('meta_pages', [
            'id' => $this->primaryKey(),
            'meta_tag_id' => $this->integer()->notNull()->comment('Meta tag'),
            'module' => $this->string()->notNull()->comment('Module Name'),
            'controller' => $this->string()->notNull()->comment('Controller name'),
            'action' => $this->string()->notNull()->comment('Action name'),
        ]);

        // add foreign key for table `meta_tags`
        $this->addForeignKey(
            'fk_meta_pages-meta_tag_id',
            'meta_pages',
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
            'fk_meta_pages-meta_tag_id',
            'meta_pages'
        );

        $this->dropTable('meta_pages');
    }
}
