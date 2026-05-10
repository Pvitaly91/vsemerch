<?php

use yii\db\Migration;

/**
 * Class m200701_144532_shop_edit_alter_partner_id
 */
class m270701_144532_shop_edit_alter_partner_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('shop_product', 'partner_id', $this->bigInteger());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }

}
