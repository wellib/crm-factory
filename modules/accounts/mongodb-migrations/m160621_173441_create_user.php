<?php

class m160621_173441_create_user extends \yii\mongodb\Migration
{
    public function up()
    {
        $this->createCollection('accounts_user');
    }

    public function down()
    {
        $this->dropCollection('accounts_user');
    }
}
