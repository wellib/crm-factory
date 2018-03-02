<?php

class m160725_011602_create_calendar_period_collection extends \yii\mongodb\Migration
{
    public function up()
    {
        $this->createCollection('todo_calendar_period');
    }

    public function down()
    {
        $this->dropCollection('todo_calendar_period');
    }
}
