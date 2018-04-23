<?php

use yii\db\Migration;

/**
 * Handles the creation of table `settings_group`.
 */
class m180325_173325_create_settings_group_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('settings_group', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->comment('Group name'),
            'description' => $this->text()->comment('Group description'),
            'icon' => $this->string()->comment('Group icon'),
            'position' => $this->integer()->comment('Order'),
        ]);

        $this->addColumn('settings', 'group_id', $this->integer()->null()->comment('Settings group'));

        $this->addForeignKey(
            'fk_settings_to_settings_group',
            'settings', 'group_id',
            'settings_group', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addColumn('settings', 'position', $this->integer()->null());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('settings_group');
    }
}
