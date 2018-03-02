<?php

namespace app\modules\hr\models\embedded;

use MongoDB\BSON\ObjectID;

use app\modules\hr\Module;
use app\modules\hr\models\Employee;

use app\modules\hr\models\DictionaryWord;
use app\modules\hr\validators\DictionaryWordValidator;
use app\modules\hr\traits\DictionaryWordEmbedded;


/**
 * Class Contact
 * @package app\modules\hr\models\employee
 */
class Contact extends EmbeddedModel
{
    use DictionaryWordEmbedded;
    /** @var ObjectID */
    public $__type;
    /** @var string */
    public $value;
    /** @var string */
    public $description;
    /** @var boolean */
    public $main;
    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['value', 'string', 'max' => 255],
            ['description', 'string'],
            ['main', 'filter', 'filter' => 'intval'],
            ['main', 'boolean'],
            ['__type', DictionaryWordValidator::className(), 'dictionary' => DictionaryWord::DICTIONARY_CONTACT_TYPE],
            [['value', '__type'], 'required'],
        ];
    }
    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            '__type'      => Module::t('contact', 'ATTR__TYPE__LABEL'),
            'value'       => Module::t('contact', 'ATTR__VALUE__LABEL'),
            'description' => Module::t('contact', 'ATTR__DESCRIPTION__LABEL'),
            'main'        => Module::t('contact', 'ATTR__MAIN__LABEL'),
        ];
    }
    /**
     * Тип контакта
     * @return string|null
     */
    public function getType()
    {
        if ($model = $this->getDictionaryWordModelByAttribute('__type')) {
            return $model->getWord();
        }
        return null;
    }

    /**
     * Это основной контакт
     * @return bool
     */
    public function isMain()
    {
        return $this->main == 1;
    }


    public static function exportColumnsConfig($attribute)
    {
        return [
            [
                'attribute' => $attribute,
                'label' => Module::t('contact', 'MODEL_NAME_PLURAL'),
                'format' => 'raw',
                'value' => function($model) {

                    /** @var Employee $model */
                    $items = array_map(function($eModel) {
                        /** @var Contact $eModel */
                        $data = [
                            $eModel->getType(),
                            $eModel->value,
                        ];
                        return implode(': ', $data);
                    }, (array) $model->contacts);
                    if (empty($items)) {
                        return null;
                    }
                    $itemsInline = implode("\n", $items);
                    return $itemsInline;
                }
            ],
        ];
    }
}