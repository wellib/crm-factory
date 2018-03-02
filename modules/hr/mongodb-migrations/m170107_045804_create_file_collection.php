<?php

class m170107_045804_create_file_collection extends \yii\mongodb\Migration
{
    public function up()
    {
        $this->createCollection('hr_file');
    }

    public function down()
    {
        $this->dropCollection('hr_file');
    }
}
