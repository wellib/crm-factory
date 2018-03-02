<?php

namespace app\modules\hr\models\embedded;

use MongoDB\BSON\ObjectID;

use app\modules\hr\Module;
use app\modules\hr\models\Employee;

use app\modules\hr\models\DictionaryWord;
use app\modules\hr\validators\DictionaryWordValidator;
use app\modules\hr\traits\DictionaryWordEmbedded;

/**
 * Class Family
 * 
 * @property ObjectID $__kinship
 * @property string $full_name
 * @property string $birth_date
 * @property string $note
 * 
 * @package app\modules\hr\models\embedded
 */
class Family extends EmbeddedModel
{
    use DictionaryWordEmbedded;

    const DATE_REGEXP_PATTERN = '/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4}$/i';
    const DATE_REGEXP_PATTERN_LABEL = 'дд.мм.гггг';
    const DATE_PHP_FORMAT = 'd.m.Y';

    /** @var ObjectID */
    public $__kinship;
    /** @var string */
    public $full_name;
    /** @var string */
    public $birth_date;
    /** @var string */
    public $note;
    
    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['full_name', 'string', 'max' => 255],

            ['note', 'string'],

            [['birth_date'], 'match',
                'pattern' => self::DATE_REGEXP_PATTERN,
                'message' => Module::t('family', 'DATE__VALIDATE_MESSAGE__BAD_DATE', [
                    'format' => self::DATE_REGEXP_PATTERN_LABEL,
                ]),
            ],

            ['__kinship', DictionaryWordValidator::className(), 'dictionary' => DictionaryWord::DICTIONARY_KINSHIP],

            [['full_name', '__kinship'], 'required'],
        ];
    }
    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            '__kinship'  => Module::t('family', 'ATTR__KINSHIP__LABEL'),
            'full_name'  => Module::t('family', 'ATTR__FULL_NAME__LABEL'),
            'birth_date' => Module::t('family', 'ATTR__BIRTH_DATE__LABEL'),
            'note'       => Module::t('family', 'ATTR__NOTE__LABEL'),
        ];
    }
    /**
     * Степень родства
     * @return string|null
     */
    public function getKinship()
    {
        if ($model = $this->getDictionaryWordModelByAttribute('__kinship')) {
            return $model->getWord();
        }
        return null;
    }

    public static function exportColumnsConfig($attribute)
    {
        return [
            [
                'attribute' => $attribute,
                'label' => Module::t('family', 'MODEL_NAME_PLURAL'),
                'format' => 'raw',
                'value' => function($model) {
                    /** @var Employee $model */
                    $items = array_map(function($eModel) {
                        /** @var Family $eModel */
                        $data = [
                            $eModel->getKinship(),
                            $eModel->full_name,
                            $eModel->birth_date,
                            $eModel->note,
                        ];
                        return implode(', ', $data);
                    }, (array) $model->family);
                    $itemsInline = implode("\n", $items);
                    if (empty($items)) {
                        return null;
                    }
                    return $itemsInline;
                }
            ],
        ];
    }
}