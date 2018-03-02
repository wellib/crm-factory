<?php

namespace app\modules\hr\validators;

use yii\validators\Validator;
use yii\base\NotSupportedException;

use app\modules\hr\models\DictionaryWord;
use app\modules\hr\Module;

/**
 * Class DictionaryWordValidator
 * @package app\modules\hr\models\validators
 */
class DictionaryWordValidator extends Validator
{
    /**
     * See constants DICTIONARY_* in \app\modules\hr\models\DictionaryWord
     * @var integer|null
     * @see DictionaryWord
     */
    public $dictionary = null;

    public $isEmpty = null;
    
    //public function validateAttributes($model, $attributes = null)
    //{
    //    foreach ($attributes as $attribute) {
    //        $this->validateAttribute($model, $attribute);
    //    }
    //}

    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        $dictionaryWordModel = DictionaryWord::find()->id($value)->one();
        if ($dictionaryWordModel) {
            if ($this->dictionary !== null && $dictionaryWordModel->dictionary !== $this->dictionary) {
                $this->addError($model, $attribute, Module::t('dictionary-word', 'VALIDATOR__NO_RELATION_TO_DICTIONARY', [
                    'dictionary' => DictionaryWord::dictionaryLabel($this->dictionary),
                ]));
            } else {
                $model->$attribute = $dictionaryWordModel->getId(false);
            }
        } else {
            $this->addError($model, $attribute, Module::t('dictionary-word', 'VALIDATOR__ID_DOES_NOT_EXIST_IN_DATABASE'));
        }

    }

    //public function isEmpty($value)
    //{
    //    if ($this->isEmpty !== null) {
    //        return call_user_func($this->isEmpty, $value);
    //    } else {
    //        return $value === null || $value === [] || $value === null;
    //    }
    //}

    protected function validateValue($value)
    {
        throw new NotSupportedException(get_class($this) . ' does not support validateValue().');
    }
}