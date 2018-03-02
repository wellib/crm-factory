<?php

namespace app\modules\hr\models;





use Yii;

use MongoDB\BSON\ObjectID;

use yii2tech\embedded\mongodb\ActiveRecord;

use yii\behaviors\TimestampBehavior;

use app\modules\hr\Module;

use app\modules\hr\models\embedded\EmbeddedModel;
use app\modules\hr\models\embedded\Hiring;
use app\modules\hr\models\embedded\BusinessTrip;
use app\modules\hr\models\embedded\Fired;

use yii\helpers\Html;
use yii\helpers\Url;


/**
 * This is the model class for collection "hr_order".
 *
 * @property ObjectID|string $_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $type
 * @property string $number
 * @property string $date
 * @property integer $date_unixtime
 * @property string $note
 *
 * @property array $_employees
 * @property Employee[] $employees
 *
 * @property array $_hiring
 * @property Hiring $hiring
 *
 * @property array $_business_trip
 * @property BusinessTrip $businessTrip
 *
 * @property array $_fired
 * @property Fired $fired
 */
class Order extends ActiveRecord
{
    /** Тип: прием на работу */
    const TYPE__HIRING = 100;
    /** Тип: отпуск */
    const TYPE__VACATION = 200;
    /** Тип: Командировка */
    const TYPE__BUSINESS_TRIP = 300;
    /** Тип: увольнение */
    const TYPE__FIRED = 400;

    /** Шаблон регулярного выражения для валидации даты в формате дд.мм.гггг */
    const BIRTHDAY_DATE_REGEXP_PATTERN = '/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4}$/i';
    const BIRTHDAY_DATE_REGEXP_PATTERN_LABEL = 'дд.мм.гггг';
    const BIRTHDAY_DATE_PHP_FORMAT = 'd.m.Y';
    
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'hr_order';
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

            // type
            'type',

            // common attributes
            'number',
            'date',
            'date_unixtime',
            'note',

            '_employees',

            '_hiring',
            '_business_trip',
            '_fired',
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => [
                'number',
                'date',
                'note',
                '_employees',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['number', 'string', 'max' => 255],

            ['note', 'string'],
            ['note', 'default', 'value' => null],

            ['date', 'match',
                'pattern' => self::BIRTHDAY_DATE_REGEXP_PATTERN,
                'message' => Module::t('order', 'ATTR__DATE__VALIDATE_MESSAGE__BAD_DATE', [
                    'format' => self::BIRTHDAY_DATE_REGEXP_PATTERN_LABEL,
                ]),
            ],

            ['_employees', 'each', 'rule' => ['filter', 'filter' => function($value) {
                if ($value instanceof ObjectID) {
                    return $value;
                } else {
                    return new ObjectID((string) $value);
                }
            }]],

            [['_hiring', '_business_trip', '_fired'], 'yii2tech\embedded\Validator'],

            [['number', 'date', '_employees'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            // system attributes
            '_id'        => Module::t('order', 'ATTR__ID__LABEL'),
            'created_at' => Module::t('order', 'ATTR__CREATED_AT__LABEL'),
            'updated_at' => Module::t('order', 'ATTR__UPDATED_AT__LABEL'),

            // type
            'type' => Module::t('order', 'ATTR__TYPE__LABEL'),

            // common attributes
            'number' => Module::t('order', 'ATTR__NUMBER__LABEL'),
            'date'   => Module::t('order', 'ATTR__DATE__LABEL'),
            'note'   => Module::t('order', 'ATTR__NOTE__LABEL'),

            '_employees' => Module::t('order', 'ATTR__EMPLOYEES__LABEL'),
        ];
    }

    /**
     * @return OrderQuery
     */
    public static function find()
    {
        return new OrderQuery(get_called_class());
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
     * @return \yii\db\ActiveQueryInterface
     */
    public function getEmployees()
    {
        return $this->hasMany(Employee::className(), ['_id' => '_employees']);
    }

    /**
     * @return \yii2tech\embedded\Mapping
     */
    public function embedHiring()
    {
        return $this->mapEmbedded('_hiring', Hiring::className());
    }

    /**
     * @return \yii2tech\embedded\Mapping
     */
    public function embedBusinessTrip()
    {
        return $this->mapEmbedded('_business_trip', BusinessTrip::className());
    }

    /**
     * @return \yii2tech\embedded\Mapping
     */
    public function embedFired()
    {
        return $this->mapEmbedded('_fired', Fired::className());
    }


    /**
     * @return array
     */
    public static function getEmbeddedModelsConfig()
    {
        return [
            self::TYPE__HIRING => [
                'modelAttribute' => 'hiring',
                'viewDir'        => 'hiring',
                'className'      => Hiring::className(),
            ],
            self::TYPE__BUSINESS_TRIP => [
                'modelAttribute' => 'businessTrip',
                'viewDir'        => 'business_trip',
                'className'      => BusinessTrip::className(),
            ],
            self::TYPE__FIRED => [
                'modelAttribute' => 'fired',
                'viewDir'        => 'fired',
                'className'      => Fired::className(),
            ],
        ];
    }

    /**
     * @return EmbeddedModel|null
     */
    public function getEmbeddedModelByType()
    {
        $config = self::getEmbeddedModelsConfig();
        if (isset($config[$this->type])) {
            return $this->{$config[$this->type]['modelAttribute']};
        }
        return null;
    }

    /**
     * @return null|string
     */
    public function getEmbeddedModelViewDir()
    {
        $config = self::getEmbeddedModelsConfig();
        if (isset($config[$this->type])) {
            return $config[$this->type]['viewDir'];
        }
        return null;
    }

    public function afterValidate()
    {
        $this->date_unixtime = !empty($this->date) ? strtotime($this->date) : null;
        parent::afterValidate(); // TODO: Change the autogenerated stub
    }

    /**
     * @param bool $toString
     * @return ObjectID|string
     */
    public function getId($toString = true)
    {
        return $toString ? (string) $this->_id : $this->_id;
    }

    /**
     * Route to view
     * @return array
     */
    public function getViewRoute()
    {
        return ['/hr/order/view', 'id' => $this->getId(true)];
    }

    /**
     * URL to view
     * @param bool $scheme
     * @return string
     */
    public function getViewUrl($scheme =  false)
    {
        return Url::to($this->getViewRoute(), $scheme);
    }

    /**
     * Main order title
     * @return string
     */
    public function getTitle()
    {
        return Html::encode("{$this->number} ({$this->date})");
    }

    /**
     * Types labels<br/>
     * Value => Label 
     * @return array
     */
    public static function typesLabels()
    {
        return [
            self::TYPE__HIRING        => Module::t('order', 'TYPE__HIRING__LABEL'),
            //self::TYPE__VACATION      => Module::t('order', 'TYPE__VACATION__LABEL'),
            self::TYPE__BUSINESS_TRIP => Module::t('order', 'TYPE__BUSINESS_TRIP__LABEL'),
            self::TYPE__FIRED         => Module::t('order', 'TYPE__FIRED__LABEL'),
        ];
    }

    /**
     * Check, type is exist or not
     * @param integer $type
     * @return bool
     */
    public static function typeIsExist($type)
    {
        $labels = self::typesLabels();
        return isset($labels[$type]);
    }

    /**
     * Type label
     * @return string
     */
    public function getTypeLabel()
    {
        $labels = self::typesLabels();
        if (isset($labels[$this->type])) {
            return $labels[$this->type];
        }
        return Module::t('order', 'TYPE__UNKNOWN');
    }

    public static function exportColumnsConfig()
    {
        return [
            [
                'attribute' => 'type',
                'value' => function($model) {
                    /** @var Order $model */
                    return $model->getTypeLabel();
                },
            ],
            [
                'attribute' => '_employees',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var Order $model */
                    $employeesList = array_map(function($employee){
                        /** @var Employee $employee */
                        return $employee->getFullName();
                    }, $model->employees);
                    return implode(', ', $employeesList);
                },
            ],
            [
                //'attribute' => 'structure_object',
                'label' => \app\modules\structure\Module::t('department', 'MODEL_NAME'),
                'format' => 'raw',
                'value' => function($model) {
                    /** @var Order $model */
                    $items = [];
                    foreach ($model->employees as $employee) {
                        $employee->companyCard->getStructureDepartment(true, true);
                        $items[(string) $employee->companyCard->_structure_department] = $employee->companyCard->getStructureDepartment(true, true);
                    }
                    if ($items > 0) {
                        return implode("\n", $items);
                    }
                    return null;
                },
            ],
            'number',
            [
                'attribute' => 'date',
            ],
        ];
    }
}
