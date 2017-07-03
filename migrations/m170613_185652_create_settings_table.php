<?php

use yii\db\Migration;

/**
 * Handles the creation of table `settings`.
 */
class m170613_185652_create_settings_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('settings', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'key' => $this->string(75)->notNull(),
            'value' => $this->text()->null(),
            'type' => $this->integer()->notNull(),
            'type_settings' => $this->text(),
        ]);

        $this->createIndex('unq_settings_key', 'settings', 'key', true);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('settings');
    }
}
