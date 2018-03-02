<?php

namespace app\modules\hr\traits;

use app\modules\hr\models\DictionaryWord;

trait DictionaryWordEmbedded
{
    /**
     * @param string $attribute
     * @return DictionaryWord|null
     */
    protected function getDictionaryWordModelByAttribute($attribute)
    {
        if (!empty($this->$attribute)) {
            return DictionaryWord::find()->id($this->$attribute)->one();
        }
        return null;
    }
}