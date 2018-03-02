<?php

class m161222_190010_create_employee_collection extends \yii\mongodb\Migration
{
    public function up()
    {
        $this->createCollection('hr_employee');
    }

    public function down()
    {
        $this->dropCollection('hr_employee');
    }
}
