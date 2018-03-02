<?php

class m160825_040022_company extends \yii\mongodb\Migration
{
    public function up()
    {
        $this->createCollection('docs_company');
    }

    public function down()
    {
        $this->dropCollection('docs_company');
    }
}
