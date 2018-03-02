<?php

class m160710_131834_create_task_comments extends \yii\mongodb\Migration
{
    public function up()
    {
        $this->createCollection('todo_task_comments');
    }

    public function down()
    {
        $this->dropCollection('todo_task_comments');
    }
}
