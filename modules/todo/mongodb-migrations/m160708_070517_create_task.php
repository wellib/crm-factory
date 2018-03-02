<?php

class m160708_070517_create_task extends \yii\mongodb\Migration
{
    public function up()
    {
        $this->createCollection('todo_task');
    }

    public function down()
    {
        $this->dropCollection('todo_task');
    }
}
