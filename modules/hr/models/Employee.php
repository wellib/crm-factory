<?php

namespace app\modules\hr\models;



use Yii;

use yii\behaviors\TimestampBehavior;

use MongoDB\BSON\ObjectID;
use yii\bootstrap\Html;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii2tech\embedded\mongodb\ActiveRecord;

use yii\helpers\ArrayHelper;
use app\helpers\StringHelper;

use app\modules\hr\Module;
use app\modules\hr\models\embedded\IdentityCard;
use app\modules\hr\models\embedded\CompanyCard;
use app\modules\hr\models\embedded\Contact;
use app\modules\hr\models\embedded\Education;
use app\modules\hr\models\embedded\Family;
use app\modules\hr\models\embedded\Experience;

use app\modules\accounts\models\User;

/**
 * This is the model class for collection "hr_employee".
 *
 * @property ObjectID|string $_id
 * @property integer $created_at
 * @property integer $updated_at
 *
 *
 * @property string $first_name  Имя
 * @property string $middle_name Отчество
 * @property string $last_name   Фамилия
 * @property string $full_name   Полное имя - Фамилия Имя Отчество
 * @property string $last_name_with_initials Фамилия и инициалы
 * @property string $sex         Пол (мужской или женский)
 *
 * @property string  $birthday          День рождения (строковое) - используется в форме
 * @property integer $birthday_unixtime День рождения (unix timestamp) - используется для выборки
 * 
 * @property string $position Должность
 *
 * @property ObjectID|string|null $_user MongoDB id пользователя привязанного к данному сотруднику
 * @property User $user Модель пользователя привязанного к данному сотруднику (Relation model)
 *
 *
 * @property array|null $_identity_card
 * @property IdentityCard|null $identityCard
 *
 * @property array|null $_company_card
 * @property CompanyCard|null $companyCard
 *
 * @property array|null $_contacts
 * @property Contact[]|array $contacts
 *
 * @property array|null $_educations
 * @property Education[]|array $educations
 *
 * @property array|null $_family
 * @property Family[]|array $family
 *
 * @property array|null $_experience
 * @property Experience[]|array $experience
 *
 * @property ObjectID[]|array|null $_files
 * @property File[]|array $files
 */
class Employee extends ActiveRecord
{

    const SEX_MALE = 1;
    const SEX_FEMALE = 2;

    /**
     * Шаблон регулярного выражения для валидации даты в формате дд.мм.гггг
     */
    const BIRTHDAY_DATE_REGEXP_PATTERN = '/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4}$/i';
    const BIRTHDAY_DATE_REGEXP_PATTERN_LABEL = 'дд.мм.гггг';
    const BIRTHDAY_DATE_PHP_FORMAT = 'd.m.Y';


    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'hr_employee';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            // system attributes
            '_id',
            'created_at',
            'updated_at',
            //

            'first_name',
            'middle_name',
            'last_name',
            'full_name',
            'last_name_with_initials',

            'sex',
            'birthday',
            'birthday_unixtime',
            'position',
            '_user',

            '_identity_card',
            '_company_card',

            '_contacts',
            '_educations',
            '_family',
            '_experience',
            '_files',

        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => [
                'first_name',
                'middle_name',
                'last_name',
                'sex',
                'birthday',
                'position',
                '_user',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_name', 'middle_name', 'last_name', 'position'], 'string', 'max' => 255],
            [['first_name', 'middle_name', 'last_name', 'position'], 'filter', 'filter' => 'trim', 'skipOnArray' => true],

            [['first_name', 'last_name', 'sex', 'birthday', 'position'], 'required'],

            ['sex', 'filter', 'filter' => 'intval'],
            ['sex', 'in', 'range' => array_keys($this->getSexList())],

            ['birthday', 'match',
                'pattern' => self::BIRTHDAY_DATE_REGEXP_PATTERN,
                'message' => Module::t('employee', 'ATTR__BIRTHDAY__VALIDATE_MESSAGE__BAD_DATE', [
                    'format' => self::BIRTHDAY_DATE_REGEXP_PATTERN_LABEL,
                ]),
            ],

            ['_user', function ($attribute) {
                $model = User::find()->id($this->$attribute)->one();
                if ($model) {
                    $this->$attribute = $model->getId(false);
                    /**
                     * TODO: Тут нужно сделать проверку на то привязан ли выбранный пользователь к другому сотруднику
                     * TODO: Возможно лучше это отсеять еще на этапе выборки для списка, т.е. что бы в список пользователь для выбора не попадали пользователи у которых уже есть привязка
                     */
                } else {
                    $this->addError($attribute, Module::t('employee', 'ATTR__USER__VALIDATE_MESSAGE__UNKNOWN_USER'));
                }
            }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Module::t('employee', 'ATTR__ID__LABEL'),
            'created_at' => Module::t('employee', 'ATTR__CREATED_AT__LABEL'),
            'updated_at' => Module::t('employee', 'ATTR__UPDATED_AT__LABEL'),

            'first_name' => Module::t('employee', 'ATTR__FIRST_NAME__LABEL'),
            'middle_name' => Module::t('employee', 'ATTR__MIDDLE_NAME__LABEL'),
            'last_name' => Module::t('employee', 'ATTR__LAST_NAME__LABEL'),
            'full_name' => Module::t('employee', 'ATTR__FULL_NAME__LABEL'),
            'last_name_with_initials' => Module::t('employee', 'ATTR__LAST_NAME_WITH_INITIALS__LABEL'),
            'sex' => Module::t('employee', 'ATTR__SEX__LABEL'),

            'birthday' => Module::t('employee', 'ATTR__BIRTHDAY__LABEL'),
            'birthday_unixtime' => Module::t('employee', 'ATTR__BIRTHDAY__LABEL'),
            
            'position' => Module::t('employee', 'ATTR__POSITION__LABEL'),

            '_user' => Module::t('employee', 'ATTR__USER__LABEL'),
        ];
    }

    public function afterValidate()
    {
        $this->setBirthdayUnixTimestamp();
        $this->setFullName();
        $this->setLastNameWithInitials();
        return parent::afterValidate(); // TODO: Change the autogenerated stub
    }

    /**
     * Установить unix timestamp значение даты рождения<br>
     * @return bool
     */
    public function setBirthdayUnixTimestamp()
    {
        if (($birthday_timestamp = strtotime($this->birthday)) !== false) {
            $this->birthday_unixtime = $birthday_timestamp;
        } else {
            $this->birthday_unixtime = null;
        }
        return true;
    }

    /**
     * Установить значение полного имени - Фамилия Имя Отчество
     * @return bool
     */
    public function setFullName()
    {
        $this->full_name = $this->getFullName(false);
        return true;
    }

    /**
     * Установить значение фамилии с инициалами в формате "Фамилия И.О."
     * @return bool
     */
    public function setLastNameWithInitials()
    {
        $this->last_name_with_initials = $this->getFullName(true);
        return true;
    }

    /**
     * Полное имя - Фамилия Имя Отчество
     * @param bool $initials Вернуть в виде фамилии и инициалов имени и отчества в формате "Фамилия И.О."
     * @return string
     */
    public function getFullName($initials = false)
    {
        return implode(' ', [
            $this->last_name,
            $initials ? StringHelper::firstLetter($this->first_name, true) . '.' : $this->first_name,
            $initials ? StringHelper::firstLetter($this->middle_name, true) . '.' : $this->middle_name,
        ]);
    }

    /**
     * @return EmployeeQuery
     */
    public static function find()
    {
        return new EmployeeQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['_id' => '_user']);
    }
    
    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getFiles()
    {
        return $this->hasMany(File::className(), ['_id' => '_files']);
    }
    
    /**
     * @return \yii2tech\embedded\Mapping
     */
    public function embedIdentityCard()
    {
        return $this->mapEmbedded('_identity_card', IdentityCard::className());
    }
    /**
     * @return \yii2tech\embedded\Mapping
     */
    public function embedCompanyCard()
    {
        return $this->mapEmbedded('_company_card', CompanyCard::className());
    }
    /**
     * @return \yii2tech\embedded\Mapping
     */
    public function embedContacts()
    {
        return $this->mapEmbeddedList('_contacts', Contact::className());
    }
    /**
     * @return \yii2tech\embedded\Mapping
     */
    public function embedEducations()
    {
        return $this->mapEmbeddedList('_educations', Education::className());
    }
    /**
     * @return \yii2tech\embedded\Mapping
     */
    public function embedFamily()
    {
        return $this->mapEmbeddedList('_family', Family::className());
    }
    /**
     * @return \yii2tech\embedded\Mapping
     */
    public function embedExperience()
    {
        return $this->mapEmbeddedList('_experience', Experience::className());
    }
    /**
     * @return ArrayDataProvider
     */
    public function getContactsDataProvider()
    {
        return new ArrayDataProvider([
            'allModels' => (array) $this->contacts,
        ]);
    }

    /**
     * Список полов (мужской и женский)
     * @return array
     */
    public static function sexList()
    {
        return [
            self::SEX_MALE => Module::t('employee', 'SEX_MALE'),
            self::SEX_FEMALE => Module::t('employee', 'SEX_FEMALE'),
        ];
    }

    /**
     * Список полов (мужской и женский)<br/>
     * <b>Порт для static метода</b>
     * @return array
     */
    public function getSexList()
    {
        return self::sexList();
    }

    /**
     * Пол
     * @return mixed|null Если null то пол либо не указан либо его нет в списке
     */
    public function getSexLabel()
    {
        $list = $this->getSexList();
        if (isset($list[$this->sex])) {
            return $list[$this->sex];
        }
        return null;
    }

    /**
     * Список пользователей для dropDownList
     * @return array
     */
    public function getUsers()
    {
        $models = User::find()->all();
        return ArrayHelper::map($models, function ($model) {
            /** @var User $model */
            return (string)$model->getId();
        }, function ($model) {
            /** @var User $model */
            return $model->getNameAndPosition();
        });
    }

    /**
     * Имя аккаунта сотрудника в системе
     *
     * @param bool $returnLink Вернет в виде ссылки [a href]
     * @param array $linkOptions
     * @return null|string
     */
    public function getUserName($returnLink = false, $linkOptions = ['target' => '_blank'])
    {
        if ($this->user instanceof User) {
            $username = $this->user->getNameAndPosition();
            if ($returnLink) {
                return Html::a($username, $this->user->getViewUrl(), $linkOptions);
            } else {
                return $username;
            }
        }
        return null;
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
     * URL to view detailed info
     * @param bool $scheme
     * @return string
     */
    public function getViewUrl($scheme = false)
    {
        return Url::to(['/hr/employee/view', 'id' => $this->getId(true)], $scheme);
    }

    /**
     * @return array|Contact[]
     */
    public function getMainContacts()
    {
        return array_filter((array)$this->contacts, function ($contact) {
            /** @var Contact $contact */
            return $contact->isMain();
        });
    }

    public static function exportColumnsConfig()
    {
        return [
            [
                'attribute' => 'last_name_with_initials',
            ],
            [
                'attribute' => 'full_name',
            ],
            [
                'attribute' => 'last_name',
            ],
            [
                'attribute' => 'first_name',
            ],
            [
                'attribute' => 'middle_name',
            ],
            [
                'attribute' => 'sex',
                'value' => function($model) {
                    /** @var Employee $model */
                    return $model->getSexLabel();
                }
            ],
            [
                'attribute' => 'birthday',
            ],
            [
                'attribute' => 'position',
            ],
            [
                'attribute' => '_user',
                'value' => function($model) {
                    /** @var Employee $model */
                    if ($model->user) {
                        return $model->user->getNameAndPosition();
                    }
                    return null;
                }
            ],
        ];
    }

    /**
     * key (attribute) => value (model class name)
     * @return array
     */
    public static function getEmbeddedModelsConfig()
    {
        return [
            'identityCard' => IdentityCard::className(),
            'companyCard'  => CompanyCard::className(),
            'contacts'     => Contact::className(),
            'educations'   => Education::className(),
            'family'       => Family::className(),
            'experience'   => Experience::className(),
            //'files' => File::className(),
        ];
    }
}
