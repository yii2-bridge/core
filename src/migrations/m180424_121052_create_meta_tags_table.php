<?php

use yii\db\Migration;

/**
 * Handles the creation of table `meta_tags`.
 */
class m180424_121052_create_meta_tags_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('meta_tags', [
            'id' => $this->primaryKey(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP()')->comment('Дата создания'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP()')->comment('Дата редактирования'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('meta_tags');
    }
}
