<?php

use yii\db\Migration;

/**
 * Handles the creation of table `users`.
 */
class m170604_192339_create_users_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('users', [
            'id' => $this->primaryKey(),
            'username' => $this->string('50')->notNull()->unique(),
            'password_hash' => $this->string()->notNull(),
            'avatar' => $this->string(),
            'access_token' => $this->string()->notNull(),
            'is_active' => $this->boolean()->notNull()->defaultValue(1),
            'created_at' => $this->timestamp()->null()->defaultExpression('NULL'),
            'updated_at' => $this->timestamp()->null()->defaultExpression('NULL'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('users');
    }
}
