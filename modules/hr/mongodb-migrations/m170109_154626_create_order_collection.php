<?php

class m170109_154626_create_order_collection extends \yii\mongodb\Migration
{
    public function up()
    {
        $this->createCollection('hr_order');
    }

    public function down()
    {
        $this->dropCollection('hr_order');
    }
}
