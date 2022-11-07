<?php

use yii\db\Migration;

/**
 * Class m221107_172607_add_firstname_lastname_columns_to_user
 */
class m221107_172607_add_firstname_lastname_columns_to_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}','firstname',$this->string(255)->notNull()->after('id'));
        $this->addColumn('{{%user}}','lastname',$this->string(255)->notNull()->after('firstname'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}','firstname');
        $this->dropColumn('{{%user}}','lastname');
    }

}
