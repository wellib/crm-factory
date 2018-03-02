<?php

namespace app\modules\hr\models\embedded;

use MongoDB\BSON\ObjectID;

use app\modules\hr\Module;
use app\modules\hr\models\Employee;

use app\modules\hr\models\DictionaryWord;
use app\modules\hr\validators\DictionaryWordValidator;
use app\modules\hr\traits\DictionaryWordEmbedded;

/**
 * Class Experience
 *
 * @property string $start_date
 * @property string $end_date
 * @property string $organization
 * @property string $position
 * @property ObjectID $__dismissal_reason
 *
 * @package app\modules\hr\models\embedded
 */
class Experience extends EmbeddedModel
{
    use DictionaryWordEmbedded;

    const DATE_REGEXP_PATTERN = '/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4}$/i';
    const DATE_REGEXP_PATTERN_LABEL = 'дд.мм.гггг';
    const DATE_PHP_FORMAT = 'd.m.Y';

    /** @var string */
    public $start_date;
    /** @var string */
    public $end_date;
    /** @var string */
    public $organization;
    /** @var string */
    public $position;
    /** @var ObjectID */
    public $__dismissal_reason;
    
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['organization', 'position'], 'string', 'max' => 255],

            [['start_date', 'end_date'], 'match',
                'pattern' => self::DATE_REGEXP_PATTERN,
                'message' => Module::t('family', 'DATE__VALIDATE_MESSAGE__BAD_DATE', [
                    'format' => self::DATE_REGEXP_PATTERN_LABEL,
                ]),
            ],

            ['__dismissal_reason', DictionaryWordValidator::className(), 'dictionary' => DictionaryWord::DICTIONARY_DISMISSAL_REASON],

            [['start_date', 'organization', 'position'], 'required'],
        ];
    }
    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'start_date'         => Module::t('experience', 'ATTR__START_DATE__LABEL'),
            'end_date'           => Module::t('experience', 'ATTR__END_DATE__LABEL'),
            'organization'       => Module::t('experience', 'ATTR__ORGANIZATION__LABEL'),
            'position'           => Module::t('experience', 'ATTR__POSITION__LABEL'),
            '__dismissal_reason' => Module::t('experience', 'ATTR__DISMISSAL_REASON__LABEL'),
        ];
    }
    /**
     * Причина увольнения (из словаря)
     * @return string|null
     */
    public function getDismissalReason()
    {
        if ($model = $this->getDictionaryWordModelByAttribute('__dismissal_reason')) {
            return $model->getWord();
        }
        return null;
    }

    public static function exportColumnsConfig($attribute)
    {
        return [
            [
                'attribute' => $attribute,
                'label' => Module::t('experience', 'MODEL_NAME_PLURAL'),
                'format' => 'raw',
                'value' => function($model) {
                    /** @var Employee $model */
                    $items = array_map(function($eModel) {
                        /** @var Experience $eModel */
                        $data = [
                            $eModel->organization,
                            $eModel->position,
                            $eModel->start_date,
                            $eModel->end_date,
                            $eModel->getDismissalReason(),
                        ];
                        return implode(', ', $data);
                    }, (array) $model->experience);
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