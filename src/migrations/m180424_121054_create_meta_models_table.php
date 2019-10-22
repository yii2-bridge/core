<?php

use yii\db\Migration;

/**
 * Handles the creation of table `meta_models`.
 */
class m180424_121054_create_meta_models_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('meta_models', [
            'id' => $this->primaryKey(),
            'meta_tag_id' => $this->integer()->notNull()->comment('Meta tag'),
            'model' => $this->string()->notNull()->comment('Model class'),
            'model_id' => $this->integer()->notNull()->comment('Model entry ID'),
        ]);

        // add foreign key for table `meta_tags`
        $this->addForeignKey(
            'fk_meta_models-meta_tag_id',
            'meta_models',
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
            'fk_meta_models-meta_tag_id',
            'meta_models'
        );

        $this->dropTable('meta_models');
    }
}
