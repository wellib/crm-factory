<?php

class m161224_161806_create_dictionary_word_collection extends \yii\mongodb\Migration
{
    public function up()
    {
        $this->createCollection('hr_dictionary_word');
    }

    public function down()
    {
        $this->dropCollection('hr_dictionary_word');
    }
}
