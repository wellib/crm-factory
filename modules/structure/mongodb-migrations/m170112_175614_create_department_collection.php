<?php

class m170112_175614_create_department_collection extends \yii\mongodb\Migration
{
    public function up()
    {
        $this->createCollection('structure_department');
    }

    public function down()
    {
        $this->dropCollection('structure_department');
    }
}
