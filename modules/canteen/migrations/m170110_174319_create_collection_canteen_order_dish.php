<?php

class m170110_174319_create_collection_canteen_order_dish extends \yii\mongodb\Migration
{
    public function up()
    {
        $this->createCollection('canteen_order_dish');
    }

    public function down()
    {
        $this->dropCollection('canteen_order_dish');
    }
}
