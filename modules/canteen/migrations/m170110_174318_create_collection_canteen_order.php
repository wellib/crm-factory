<?php

class m170110_174318_create_collection_canteen_order extends \yii\mongodb\Migration
{
    public function up()
    {
        $this->createCollection('canteen_order');
    }

    public function down()
    {
        $this->dropCollection('canteen_order');
    }
}
