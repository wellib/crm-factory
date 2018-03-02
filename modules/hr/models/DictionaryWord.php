<?php

namespace app\modules\hr\models;

use Yii;
use MongoDB\BSON\ObjectID;
use yii\behaviors\TimestampBehavior;
use yii\bootstrap\Html;
use yii\mongodb\ActiveRecord;

use app\modules\hr\Module;


/**
 * This is the model class for collection "hr_dictionary_word".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property mixed $dictionary
 * @property mixed $word
 * @property mixed $created_at
 * @property mixed $updated_at
 */
class DictionaryWord extends ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    /** Словарь "Орган выдачи" */
    const DICTIONARY_ISSUING_AUTHORITY = 100;
    /** Словарь "Национальность" */
    const DICTIONARY_NATIONALITY = 200;
    /** Словарь "Семейное положение" */
    const DICTIONARY_MARITAL_STATUS = 300;
    /** Словарь "Тип контакта(e-mail, skype, tel, etc." */
    const DICTIONARY_CONTACT_TYPE = 400;
    /** Словарь "Степень родства(мама, папа, бабушка, дедушка, брат, сестра, супруг, супруга, etc.)" */
    const DICTIONARY_KINSHIP = 500;
    /** Словарь "Причина увольнения(По собственному желани, привышение должностных полномочий, по обоюдному согласию сторон, систематические пропуски)" */
    const DICTIONARY_DISMISSAL_REASON = 600;
    /** Словарь "Условия приема на работу [на кокой условный срок принимают на работу] (Постоянно, На вермя дикретного отпуска, На неопределенный срок, etc." */
    const DICTIONARY_EMPLOYMENT_TERM = 700;
    /** Словарь "Основание увольнения (По собственному желанию, По согласию сторон, По статье)" */
    const DICTIONARY_BASE_DISMISSAL = 800;


    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'hr_dictionary_word';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'dictionary',
            'word',
            'created_at',
            'updated_at',
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [
                'word',
                'dictionary',
            ],
            self::SCENARIO_UPDATE => [
                'word',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['word', 'string', 'max' => 255],
            ['word', 'filter', 'filter' => 'trim'],
            ['dictionary', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['dictionary', 'in', 'range' => array_keys(self::dictionaryList())],
            [['word', 'dictionary'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id'        => Module::t('dictionary-word', 'ATTR__ID__LABEL'),
            'dictionary' => Module::t('dictionary-word', 'ATTR__DICTIONARY__LABEL'),
            'word'       => Module::t('dictionary-word', 'ATTR__WORD__LABEL'),
            'created_at' => Module::t('dictionary-word', 'ATTR__CREATED_AT__LABEL'),
            'updated_at' => Module::t('dictionary-word', 'ATTR__UPDATED_AT__LABEL'),
        ];
    }

    /**
     * @return DictionaryWordQuery
     */
    public static function find()
    {
        return new DictionaryWordQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * Model ID
     * @param bool $toString
     * @return ObjectID|string
     */
    public function getId($toString = true)
    {
        return $toString ? (string) $this->_id : $this->_id;
    }

    /**
     * Список доступных словарей
     * @return array
     */
    public static function dictionaryList()
    {
        return [
            self::DICTIONARY_ISSUING_AUTHORITY => Module::t('dictionary-word', 'DICTIONARY_ISSUING_AUTHORITY'),
            self::DICTIONARY_NATIONALITY       => Module::t('dictionary-word', 'DICTIONARY_NATIONALITY'),
            self::DICTIONARY_MARITAL_STATUS    => Module::t('dictionary-word', 'DICTIONARY_MARITAL_STATUS'),
            self::DICTIONARY_CONTACT_TYPE      => Module::t('dictionary-word', 'DICTIONARY_CONTACT_TYPE'),
            self::DICTIONARY_KINSHIP           => Module::t('dictionary-word', 'DICTIONARY_KINSHIP'),
            self::DICTIONARY_DISMISSAL_REASON  => Module::t('dictionary-word', 'DICTIONARY_DISMISSAL_REASON'),
            self::DICTIONARY_EMPLOYMENT_TERM   => Module::t('dictionary-word', 'DICTIONARY_EMPLOYMENT_TERM'),
            self::DICTIONARY_BASE_DISMISSAL    => Module::t('dictionary-word', 'DICTIONARY_BASE_DISMISSAL'),
        ];
    }

    /**
     * Название словаря
     * @param $dictionary
     * @return mixed|string
     */
    public static function dictionaryLabel($dictionary)
    {
        $list = self::dictionaryList();
        if (isset($list[$dictionary])) {
            return $list[$dictionary];
        }
        return Module::t('dictionary-word', 'DICTIONARY_UNKNOWN');
    }

    /**
     * Слово
     * @return string
     */
    public function getWord()
    {
        return Html::encode($this->word);
    }
}
