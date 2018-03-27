<?php

use yii\db\Migration;

/**
 * Handles adding key to table `settings_group`.
 */
class m180327_163555_add_key_column_to_settings_group_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('settings_group', 'key', $this->string()->notNull()->unique());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('settings_group', 'key');
    }
}
