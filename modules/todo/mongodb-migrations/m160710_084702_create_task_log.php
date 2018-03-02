<?php

class m160710_084702_create_task_log extends \yii\mongodb\Migration
{
    public function up()
    {
        $this->createCollection('todo_task_log');
    }

    public function down()
    {
        $this->dropCollection('todo_task_log');
    }
}
