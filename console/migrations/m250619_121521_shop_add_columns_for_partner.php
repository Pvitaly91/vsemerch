<?php

use yii\db\Migration;

/**
 * Class m200619_121521_shop_add_columns_for_partner
 */
class m250619_121521_shop_add_columns_for_partner extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('shop_category', 'partner', $this->string()->null());
        $this->addColumn('shop_category', 'partner_id', $this->integer()->null());
        $this->addColumn('shop_product', 'partner', $this->string()->null());
        $this->addColumn('shop_product', 'partner_id', $this->integer()->null());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('shop_category', 'partner');
        $this->dropColumn('shop_category', 'partner_id');
        $this->dropColumn('shop_product', 'partner');
        $this->dropColumn('shop_product', 'partner_id');
    }
}
