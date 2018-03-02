<?php

class m170114_124319_create_collection_canteen_option extends \yii\mongodb\Migration
{
    public function up()
    {
        $this->createCollection('canteen_option');
    }

    public function down()
    {
        $this->dropCollection('canteen_option');
    }
}
