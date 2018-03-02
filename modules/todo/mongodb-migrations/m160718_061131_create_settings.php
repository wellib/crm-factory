<?php

class m160718_061131_create_settings extends \yii\mongodb\Migration
{
    public function up()
    {
        $this->createCollection('todo_settings');
    }

    public function down()
    {
        $this->dropCollection('todo_settings');
    }
}
