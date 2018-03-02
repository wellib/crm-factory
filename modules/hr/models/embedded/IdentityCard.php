<?php

namespace app\modules\hr\models\embedded;

use MongoDB\BSON\ObjectID;

use yii\base\Model;

use app\modules\hr\Module;
use app\modules\hr\models\Employee;
use app\modules\hr\models\DictionaryWord;
use app\modules\hr\validators\DictionaryWordValidator;


/**
 * Модель - Удостоверение личности
 * 
 * Class IdentityCard
 * 
 * @property string $id_number
 * @property string $vat_id
 * @property string $issue_date
 * @property string $birthplace
 * @property string $registration_address
 * @property string $residential_address
 * 
 * @property ObjectID|string|null $_issuing_authority
 * @property DictionaryWord|null $issuingAuthority
 * @property ObjectID|string|null $_nationality
 * @property ObjectID|string|null $_marital_status
 * 
 * @package app\modules\hr\models\employee
 */
class IdentityCard extends Model
{

    /**
     * Шаблон регулярного выражения для валидации даты в формате дд.мм.гггг
     */
    const DATE_REGEXP_PATTERN = '/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4}$/i';
    const DATE_REGEXP_PATTERN_LABEL = 'дд.мм.гггг';
    const DATE_PHP_FORMAT = 'd.m.Y';

    public $id_number;
    public $vat_id;
    public $issue_date;
    public $_issuing_authority;
    public $_nationality;
    public $birthplace;
    public $registration_address;
    public $residential_address;
    public $_marital_status;
    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => [
                'id_number',
                'vat_id',
                'issue_date',
                '_issuing_authority',
                '_nationality',
                'birthplace',
                'registration_address',
                'residential_address',
                '_marital_status',
            ],
        ];
    }
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['id_number', 'vat_id'], 'string', 'max' => 255],
            ['issue_date', 'match',
                'pattern' => self::DATE_REGEXP_PATTERN,
                'message' => Module::t('identity-card', 'ATTR__ISSUE_DATE__VALIDATE_MESSAGE__BAD_DATE', [
                    'format' => self::DATE_REGEXP_PATTERN_LABEL,
                ]),
            ],
            [['birthplace', 'registration_address', 'residential_address'], 'string', 'max' => 255 * 10],
            [['_issuing_authority'], DictionaryWordValidator::className(), 'dictionary' => DictionaryWord::DICTIONARY_ISSUING_AUTHORITY],
            [['_nationality'], DictionaryWordValidator::className(), 'dictionary' => DictionaryWord::DICTIONARY_NATIONALITY],
            [['_marital_status'], DictionaryWordValidator::className(), 'dictionary' => DictionaryWord::DICTIONARY_MARITAL_STATUS],
            [['_issuing_authority', '_nationality', '_marital_status'], 'default', 'value' => null],

            [['id_number', 'vat_id', 'issue_date', 'registration_address', '_issuing_authority'], 'required'],

        ];
    }
    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id_number'            => Module::t('identity-card', 'ATTR__ID_NUMBER__LABEL'),
            'vat_id'               => Module::t('identity-card', 'ATTR__VAT_ID__LABEL'),
            'issue_date'           => Module::t('identity-card', 'ATTR__ISSUE_DATE__LABEL'),
            '_issuing_authority'   => Module::t('identity-card', 'ATTR__ISSUING_AUTHORITY__LABEL'),
            '_nationality'         => Module::t('identity-card', 'ATTR__NATIONALITY__LABEL'),
            'birthplace'           => Module::t('identity-card', 'ATTR__BIRTHPLACE__LABEL'),
            'registration_address' => Module::t('identity-card', 'ATTR__REGISTRATION_ADDRESS__LABEL'),
            'residential_address'  => Module::t('identity-card', 'ATTR__RESIDENTIAL_ADDRESS__LABEL'),
            '_marital_status'      => Module::t('identity-card', 'ATTR__MARITAL_STATUS__LABEL'),
        ];
    }
    /**
     * Орган выдачи
     * @return DictionaryWord|null
     */
    public function getIssuingAuthority()
    {
        if ($model = $this->getDictionaryWordModelByAttribute('_issuing_authority')) {
            return $model->getWord();   
        }
        return null;
    }
    /**
     * Национальность
     * @return DictionaryWord|null
     */
    public function getNationality()
    {
        if ($model = $this->getDictionaryWordModelByAttribute('_nationality')) {
            return $model->getWord();
        }
        return null;
    }
    /**
     * Семейное положение
     * @return DictionaryWord|null
     */
    public function getMaritalStatus()
    {
        if ($model = $this->getDictionaryWordModelByAttribute('_marital_status')) {
            return $model->getWord();
        }
        return null;
    }
    /**
     * @param string $attribute
     * @return DictionaryWord|null
     */
    protected function getDictionaryWordModelByAttribute($attribute)
    {
        return DictionaryWord::find()->id($this->$attribute)->one();
    }

    protected static function labelForExportColumn($message)
    {
        return Module::t('identity-card', 'MODEL_NAME') . ': ' . Module::t('identity-card', $message);
    }

    public static function exportColumnsConfig($attribute)
    {
        return [
            [
                'attribute' => $attribute . '.id_number',
                'label' => self::labelForExportColumn('ATTR__ID_NUMBER__LABEL'),
            ],
            [
                'attribute' => $attribute . '.vat_id',
                'label' => self::labelForExportColumn('ATTR__VAT_ID__LABEL'),
            ],
            [
                'attribute' => $attribute . '._issuing_authority',
                'label' => self::labelForExportColumn('ATTR__ISSUING_AUTHORITY__LABEL'),
                'value' => function($model) {
                    /** @var Employee $model */
                    return $model->identityCard->getIssuingAuthority();
                }
            ],
            [
                'attribute' => $attribute . '._nationality',
                'label' => self::labelForExportColumn('ATTR__NATIONALITY__LABEL'),
                'value' => function($model) {
                    /** @var Employee $model */
                    return $model->identityCard->getNationality();
                }
            ],
            [
                'attribute' => $attribute . '.birthplace',
                'label' => self::labelForExportColumn('ATTR__BIRTHPLACE__LABEL'),
            ],
            [
                'attribute' => $attribute . '.registration_address',
                'label' => self::labelForExportColumn('ATTR__REGISTRATION_ADDRESS__LABEL'),
            ],
            [
                'attribute' => $attribute . '.residential_address',
                'label' => self::labelForExportColumn('ATTR__RESIDENTIAL_ADDRESS__LABEL'),
            ],
            [
                'attribute' => $attribute . '._marital_status',
                'label' => self::labelForExportColumn('ATTR__MARITAL_STATUS__LABEL'),
                'value' => function($model) {
                    /** @var Employee $model */
                    return $model->identityCard->getMaritalStatus();
                }
            ],
        ];
    }
}