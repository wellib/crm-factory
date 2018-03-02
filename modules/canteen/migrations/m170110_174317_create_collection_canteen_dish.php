<?php

class m170110_174317_create_collection_canteen_dish extends \yii\mongodb\Migration
{
    public function up()
    {
        $this->createCollection('canteen_dish');
    }

    public function down()
    {
        $this->dropCollection('canteen_dish');
    }
}
