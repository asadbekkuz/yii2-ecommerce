<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cart_item}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%product}}`
 * - `{{%user}}`
 */
class m221022_114645_create_cart_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%cart_item}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer(11)->notNull(),
            'quantity' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
        ]);

        // creates index for column `product_id`
        $this->createIndex(
            '{{%idx-cart_item-product_id}}',
            '{{%cart_item}}',
            'product_id'
        );

        // add foreign key for table `{{%product}}`
        $this->addForeignKey(
            '{{%fk-cart_item-product_id}}',
            '{{%cart_item}}',
            'product_id',
            '{{%products}}',
            'id',
            'CASCADE'
        );

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-cart_item-user_id}}',
            '{{%cart_item}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-cart_item-user_id}}',
            '{{%cart_item}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%product}}`
        $this->dropForeignKey(
            '{{%fk-cart_item-product_id}}',
            '{{%cart_item}}'
        );

        // drops index for column `product_id`
        $this->dropIndex(
            '{{%idx-cart_item-product_id}}',
            '{{%cart_item}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-cart_item-user_id}}',
            '{{%cart_item}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-cart_item-user_id}}',
            '{{%cart_item}}'
        );

        $this->dropTable('{{%cart_item}}');
    }
}
