<?php

namespace app\modules\todo\models;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\behaviors\TimestampBehavior;

use yii\web\UploadedFile;

use app\modules\todo\Module;
use app\modules\todo\validators\DateCompareValidator;

use app\modules\accounts\models\User;

/**
 * This is the model class for collection "todo_task".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property \MongoDB\BSON\ObjectID|string|null $_parent
 * @property boolean $template Является ли задача шаблонной, т.е. будут ли на её основе создаваться клоны задач - используется в задачах которые выполняются циклически (каждый день, кажую неделю, каждый месяц)
 *
 * @property integer $id          целочисленный id
 * @property string  $subject     тема(название)
 * @property integer $priority    приоритет
 * @property string  $description описание
 * @property string  $status      статус: на согласовании, выполняется, ожидает проверки результата, выполнена)
 *
 * @property integer $deadline_type        Тип срока выполнения (единоразово, каждый день, каждую неделю, каждый месяц, каждую дату)
 * @property array   $deadline_every_week  Дни недели по которым нужно выполнять
 * @property array   $deadline_every_month Дни месяца по которым нужно выполнять
 * @property array   $deadline_every_date  Даты по которым нужно выполнять
 *
 * @property string  $deadline_date      Дата до котрой нужно выполнить в формате дд.мм.гггг
 * @property string  $deadline_time      Время до которого нужно выполнить в формате чч:мм
 * @property integer $deadline_timestamp Дата и время до которого нужно выполнить в формате unix timestamp
 *
 * @property string  $perform_date      Дата с котрой нужно начать выполнение в формате дд.мм.гггг
 * @property string  $perform_time      Время с которого нужно начать выполнение в формате чч:мм
 * @property integer $perform_timestamp Дата и время с которого нужно начать выполнение в формате unix timestamp
 *
 * @property string  $start_date      Дата начала выполнения в формате дд.мм.гггг
 * @property string  $start_time      Время начала выполнения в формате чч:мм
 * @property integer $start_timestamp Дата и время начала выполнения в формате unix timestamp
 *
 * @property string  $end_date      Дата завершения выполнения в формате дд.мм.гггг
 * @property string  $end_time      Время завершения выполнения в формате чч:мм
 * @property integer $end_timestamp Дата и время завершения выполнения в формате unix timestamp
 *
 *
 * @property string $approve_execute_deadline_timestamp Крайник срок до которого нужно утвердить(начало выполнения) иначе задача будет считаться просроченной на этапе утверждения
 * @property string $check_results_deadline_timestamp  Крайник срок до которого нужно проверить результат выполненой задачи, иначе задач будет считаться просроченной на этапе проверки результата
 *

 *
 * @property array          $_attached_files     Прикпрепленные файлы которые уже сохранены на сервере
 * @property UploadedFile[] $attachedFilesUpload Прикпленные(загруженные) файлы которые ожидающие сохранения на сервере
 *
 * @property \MongoDB\BSON\ObjectID|string $_author Пользователь создавший задачу
 * @property User $author
 *
 *
 * @property array $_users_approve_execute         Пользователи которые должны одобрить данную задачу к выполнению, т.е. разрешить выполнение
 * @property User[] $usersApproveExecute
 * @property array $_users_approve_execute_response Пользователи которые дали свои ответы(разрешили или запретили выполнение задачи)
 *
 * @property array $_users_performers          Исполнители - пользователи которые выполняют(будут выполнять) данную задачу
 * @property User[] $usersPerformers
 * @property array $_users_performers_finished Исполнители которые завершили выполнение задачи
 *
 *
 * @property array $_users_check_result         Пользователи которые осуществляют проверку результата выполненной задачи, они решают выполнена задача или нет
 * @property User[] $usersCheckResult
 * @property array $_users_check_result_response Пользователи которые проверили результат выполненной задачи и дали свой ответ (положительный - приняли результат и подвердили что задача выполнена или отрицательный - не приняли результат и считают что задача не выполнена)
 *
 * @property array $_users_notify_after_finished Пользователи которые должны получить уведомление об успешном выполнении задачи после её завершения
 * @property User[] $usersNotifyAfterFinished
 *
 * @property integer $created_at unix timestamp когда была создана
 * @property integer $updated_at unix timestamp последнего изменения
 */
class Task extends \yii\mongodb\ActiveRecord
{
    // Приоритеты
    /**
     * Приоритет задачи - низкий
     */
    const PRIORITY__LOW    = 1;
    /**
     * Приоритет задачи - средний
     */
    const PRIORITY__MEDIUM = 2;
    /**
     * Приоритет задачи - высокий
     */
    const PRIORITY__HIGH   = 3;



    // Статусы
    /**
     * На согласовании (ожидает согласования)
     */
    const STATUS__APPROVAL_AWAITING = 'awaiting_approval';
    /**
     *  Не согласовано(не утверждено к выполнию) 1 или более юзеров не дали свое согласование на выполнение
     */
    const STATUS__APPROVAL_FAILED = 'approval_failed';
    /**
     * Выполняется
     */
    const STATUS__IN_PROGRESS = 'in_progress';
    /**
     * Ожидает проверки результатов выполненения
     */
    const STATUS__CHECK_RESULTS_AWAITING = 'check_results_awaiting';
    /**
     * Не прошла проверку результатов (задача не выполнена)
     */
    const STATUS__CHECK_RESULTS_FAILED = 'check_results_failed';
    /**
     * Задача выполнена
     */
    const STATUS__DONE = 'done';



    // Типы дедлайнов (сроков выполнения)
    /**
     * Разовая - выполнить 1 раз
     */
    const DEADLINE_TYPE__ONE_TIME = 'one_time';
    /**
     * Каждый день - выполнять каждый день (кроме выходных)
     */
    const DEADLINE_TYPE__EVERY_DAY = 'every_day';
    /**
     * Каждую неделю - выполнять каждую неделю, в определенные дни недели
     */
    const DEADLINE_TYPE__EVERY_WEEK = 'every_week';
    /**
     * Каждый месяц - выполнять каждый месяц, в определенные дни месяца
     */
    const DEADLINE_TYPE__EVERY_MONTH = 'every_month';
    /**
     * Каждую дату - выполнять в определенные даты
     */
    const DEADLINE_TYPE__EVERY_DATE  = 'every_date';


    /**
     * Шаблон регулярного выражения для валидации даты в формате дд.мм.гггг
     */
    const DATE_REGEXP_PATTERN = '/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4}$/i';
		const DATETIME_REGEXP_PATTERN = '/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4} [0-9]{1,2}\:[0-9]{1,2}$/i';
    const DATE_REGEXP_PATTERN_LABEL = 'дд.мм.гггг';




    /**
     * Начало списка - час
     */
    const TIME_LIST__HOUR_START = 8;
    /**
     * Окончание списка - час
     */
    const TIME_LIST__HOUR_END   = 20;
    /**
     * Кол-во минут которые используеются для шага (дробления часов)в списке
     */
    const TIME_LIST__MINUTE_STEP = 15;





    const DEADLINE_APPROVAL__PARAMS = 'now +24hours';
    const DEADLINE_CONTROL_RESULTS__PARAMS = 'now +24hours';

    /**
     * @var UploadedFile[]
     */
    public $attachedFilesUpload;
    /**
     * Максимальное кол-во файлов которые можно загрузить
     */
    const ATTACHED_FILES_UPLOAD__MAX_FILES = 10;


    /**
     * Время начал выполнения по умолчанию
     */
    const PERFORM_TIME_DEFAULT  = '08:00:00';
    /**
     * Время завершения выполнения по умолчанию
     */
    const DEADLINE_TIME_DEFAULT = '23:59:00';

    public static function collectionName()
    {
        return 'todo_task';
    }





    public function attributes()
    {
        return [
            '_id',
            '_parent',
            'template',

            'id',
            'status',

            'subject',
            'description',
            'priority',

            'deadline_type',
            'deadline_every_week',
            'deadline_every_month',
            'deadline_every_date',

            'deadline_date',
            'deadline_time',
            'deadline_timestamp',

            'perform_date',
            'perform_time',
            'perform_timestamp',

            'start_date',
            'start_time',
            'start_timestamp',

            'end_date',
            'end_time',
            'end_timestamp',

            'approve_execute_deadline_timestamp',
            'check_results_deadline_timestamp',

            '_attached_files',

            '_author',

            '_users_approve_execute',
            '_users_approve_execute_response',

            '_users_performers',
            '_users_performers_finished',

            '_users_check_result',
            '_users_check_result_response',

            '_users_notify_after_finished',

            'created_at',
            'updated_at',
        ];
    }

    public function scenarios()
    {
        return [
            self::DEADLINE_TYPE__ONE_TIME => [
                'subject',
                'description',
                'priority',

                'deadline_type',

                'perform_date',
                'deadline_date',


                '_users_approve_execute',
                '_users_performers',
                '_users_check_result',
                '_users_notify_after_finished',
            ],
            self::DEADLINE_TYPE__EVERY_DAY => [
                'subject',
                'description',
                'priority',

                'deadline_type',

                'start_date',
								'deadline_date',
                'end_date',


                '_users_approve_execute',
                '_users_performers',
                '_users_check_result',
                '_users_notify_after_finished',
            ],
            self::DEADLINE_TYPE__EVERY_WEEK => [
                'subject',
                'description',
                'priority',

                'deadline_type',
                'deadline_every_week',

                'start_date',
								'deadline_date',
                'end_date',


                '_users_approve_execute',
                '_users_performers',
                '_users_check_result',
                '_users_notify_after_finished',
            ],
            self::DEADLINE_TYPE__EVERY_MONTH => [
                'subject',
                'description',
                'priority',

                'deadline_type',
                'deadline_every_month',
                'deadline_time',

                'start_date',
								'deadline_date',
                'end_date',

                '_users_approve_execute',
                '_users_performers',
                '_users_check_result',
                '_users_notify_after_finished',
            ],
            self::DEADLINE_TYPE__EVERY_DATE => [
                'subject',
                'description',
                'priority',

								'perform_date',
                'deadline_date',
				
                'deadline_type',
                'deadline_every_date',

                '_users_approve_execute',
                '_users_performers',
                '_users_check_result',
                '_users_notify_after_finished',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subject'], 'string', 'max' => 255],
            [['description'], 'string'],
            ['priority', 'filter', 'filter' => 'intval'], // приоритеты принудительно приведет в формате целого числа, для более адекватной сортировки в базе данных
            ['priority', 'in', 'range' => array_keys($this->getPriorityList())],

            [['deadline_type'], 'in', 'range' => array_keys($this->getDeadlineTypeList())],


            [['deadline_every_week'], 'each', 'rule' => [
                'filter', 'filter' => 'intval'
            ]],
            [['deadline_every_week'], 'each', 'rule' => [
                'in', 'range' => array_keys($this->getDeadlineWeekDaysList()), 'strict' => true,
            ]],


            [['deadline_every_month'], 'each', 'rule' => [
                'filter', 'filter' => 'intval'
            ]],
            [['deadline_every_month'], 'each', 'rule' => [
                'in', 'range' => array_keys($this->getDeadlineMonthDaysList()), 'strict' => true,
            ]],


            [['deadline_every_date'], 'each', 'rule' => [
                'match', 'pattern' => self::DATE_REGEXP_PATTERN,
            ]],

            // хороший валидатор, но у него из коробки не работает client side валидиация(т.е. валидация до отправки формы) https://github.com/yiisoft/yii2/issues/7745
            //[['deadline_date', 'start_date', 'end_date'], 'date', 'format' => 'php:d.m.Y'],

            [['perform_date', 'start_date'], 'match',
                'pattern' => self::DATETIME_REGEXP_PATTERN,
                'message' => Module::t('task', 'DATE_FIELD__VALIDATE_MESSAGE__BAD_DATE', [
                    'format' => self::DATE_REGEXP_PATTERN_LABEL,
                ])
            ],

            [['deadline_date'], 'match',
                'pattern' => self::DATETIME_REGEXP_PATTERN,
                'message' => Module::t('task', 'DATETIME_FIELD__VALIDATE_MESSAGE__BAD_DATE', [
                    'format' => self::DATE_REGEXP_PATTERN_LABEL,
                ])
            ],

            [['end_date'], 'match',
                'pattern' => self::DATE_REGEXP_PATTERN,
                'message' => Module::t('task', 'DATE_FIELD__VALIDATE_MESSAGE__BAD_DATE', [
                    'format' => self::DATE_REGEXP_PATTERN_LABEL,
                ])
            ],

            //[['deadline_time', 'start_time', 'end_time'], 'in', 'range' => array_keys($this->getTimeList())],

            [[
                '_users_approve_execute',
                '_users_performers',
                '_users_check_result',
                '_users_notify_after_finished'
            ], 'default', 'value' => []],

            [
                ['attachedFilesUpload'],
                'file',
                'skipOnEmpty' => true,
                'maxFiles'    => self::ATTACHED_FILES_UPLOAD__MAX_FILES,
                'maxSize'     => 1024 * 1024 * 20, // 20 мегабайт
            ],

            [['subject', 'priority', 'deadline_type'], 'required'],

            [['perform_date', 'deadline_date'],
                'required', 'on' => self::DEADLINE_TYPE__ONE_TIME
            ],
            [['perform_date'], DateCompareValidator::className(),
                'operator' => '<=',
                'compareAttribute' => 'deadline_date',
                'on' => self::DEADLINE_TYPE__ONE_TIME
            ],
			
						['deadline_date','validateDates'],
						['end_date','validateDates'],

            //[['start_date'], '']

            [['deadline_every_week', 'start_date', 'deadline_date', 'end_date'],
                'required', 'on' => self::DEADLINE_TYPE__EVERY_DAY
            ],
            [['deadline_date', 'end_date'], DateCompareValidator::className(),
                'operator' => '>',
                'compareAttribute' => 'start_date',
                'on' => self::DEADLINE_TYPE__EVERY_DAY
            ],

            [['deadline_every_week', 'start_date', 'deadline_date', 'end_date', /*'start_time', 'end_time'*/],
                'required', 'on' => self::DEADLINE_TYPE__EVERY_WEEK
            ],
            [['deadline_date', 'end_date'], DateCompareValidator::className(),
                'operator' => '>',
                'compareAttribute' => 'start_date',
                'on' => self::DEADLINE_TYPE__EVERY_WEEK
            ],

            [['deadline_every_month', 'start_date', 'deadline_date', 'end_date'],
                'required', 'on' => self::DEADLINE_TYPE__EVERY_MONTH
            ],
            [['deadline_date', 'end_date'], DateCompareValidator::className(),
                'operator' => '>',
                'compareAttribute' => 'start_date',
                'on' => self::DEADLINE_TYPE__EVERY_MONTH
            ],


            // удаляем дубли дат



            [['deadline_every_date'],
                'required', 'on' => self::DEADLINE_TYPE__EVERY_DATE
            ],

            ['deadline_every_date', 'filter', 'filter' => 'array_unique',
                'on' => self::DEADLINE_TYPE__EVERY_DATE
            ],

            //[['deadline_every_date'], 'each', 'rule' => [
            //    DateCompareValidator::className(),
            //    'operator' => '>=',
            //    'compareValue' => date('d.m.Y', self::getToday()),
            //    'on' => self::DEADLINE_TYPE__EVERY_DATE
            //]],



            //[['deadline_every_week'], 'safe'],
            //[['deadline_every_month'], 'safe'],
            //[['deadlineTimestampFormat', 'lastDeadlineFormat'], 'match', 'pattern' => '/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4}$/i' , 'message' => Module::t('task', 'ATTR__DEADLINE_FORMAT__VALIDATE_MESSAGE__BAD_DATE', [
            //    'format' => 'дд.мм.гггг'
            //])],
            //[['deadlineTimeFormat'], function ($attribute, $params) {
            //    if (in_array($this->deadline_type, [Task::DEADLINE_TYPE_ONE_TIME])) {
            //        if (strtotime($this->deadlineTimestampFormat . ' ' . $this->deadlineTimeFormat) <= time()) {
            //            $this->addError($attribute, 'Увы, но в прошлое вернуться мы не можем!');
            //        }
            //    }
            //}],
            //[['lastDeadlineTimeFormat'], function ($attribute, $params) {
            //    if (in_array($this->deadline_type, [Task::DEADLINE_TYPE_EVERY_DAY, Task::DEADLINE_TYPE_EVERY_WEEK, Task::DEADLINE_TYPE_EVERY_MONTH])) {
            //        if (strtotime($this->lastDeadlineFormat . ' ' . $this->lastDeadlineTimeFormat) <= time()) {
            //            $this->addError($attribute, 'Увы, но в прошлое вернуться мы не можем!');
            //        }
            //    }
            //}],


            //[['_author'], 'default', 'value' => Yii::$app->getUser()->getId()], //я автор



            //[['_users_performers'], 'default', 'value' => [
            //    Yii::$app->getUser()->getId(), //если поле исполнителей пустое то я сам становлюсь исполнителем
            //]],


            //[['subject', 'priority'], 'required'],

            //[['_attached_files'], 'safe'],

            //['deadline_repeats_number', 'integer', 'max' => 10], // стоит ограничение 10, нахуйя это нужно непонятно, но клиент просил что бы было ограничение в 10 циклов
            //['deadline_repeats_number', 'default', 'value' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id'    => Module::t('task', 'ATTR__ID__LABEL'),

            'id'     => Module::t('task', 'ATTR__ID__LABEL'),
            'status' => Module::t('task', 'ATTR__STATUS__LABEL'),

            'subject'     => Module::t('task', 'ATTR__SUBJECT__LABEL'),
            'description' => Module::t('task', 'ATTR__DESCRIPTION__LABEL'),
            'priority'    => Module::t('task', 'ATTR__PRIORITY__LABEL'),

            'deadline_type'        => Module::t('task', 'ATTR__DEADLINE_TYPE__LABEL'),
            'deadline_every_week'  => Module::t('task', 'ATTR__DEADLINE_EVERY_WEEK__LABEL'),
            'deadline_every_month' => Module::t('task', 'ATTR__DEADLINE_EVERY_MONTH__LABEL'),
            'deadline_every_date'  => Module::t('task', 'ATTR__DEADLINE_EVERY_DATE__LABEL'),

            'perform_date'      => Module::t('task', 'ATTR__PERFORM_DATE__LABEL'),
            'perform_time'      => Module::t('task', 'ATTR__PERFORM_TIME__LABEL'),
            'perform_timestamp' => Module::t('task', 'ATTR__PERFORM_TIMESTAMP__LABEL'),

            'deadline_date'      => Module::t('task', 'ATTR__DEADLINE_DATE__LABEL'),
            'deadline_time'      => Module::t('task', 'ATTR__DEADLINE_TIME__LABEL'),
            'deadline_timestamp' => Module::t('task', 'ATTR__DEADLINE_TIMESTAMP__LABEL'),

            'start_date'      => Module::t('task', 'ATTR__START_DATE__LABEL'),
            'start_time'      => Module::t('task', 'ATTR__START_TIME__LABEL'),
            'start_timestamp' => Module::t('task', 'ATTR__START_TIMESTAMP__LABEL'),

            'end_date'      => Module::t('task', 'ATTR__END_DATE__LABEL'),
            'end_time'      => Module::t('task', 'ATTR__END_TIME__LABEL'),
            'end_timestamp' => Module::t('task', 'ATTR__END_TIMESTAMP__LABEL'),

            'approve_execute_deadline_timestamp' => Module::t('task', 'ATTR__APPROVE_EXECUTE_DEADLINE_TIMESTAMP__LABEL'),
            'check_results_deadline_timestamp'   => Module::t('task', 'ATTR__CHECK_RESULTS_DEADLINE_TIMESTAMP__LABEL'),

            '_attached_files'     => Module::t('task', 'ATTR__ATTACHED_FILES__LABEL'),
            'attachedFilesUpload' => Module::t('task', 'ATTR__ATTACHED_FILES_UPLOAD__LABEL'),

            '_users_approve_execute'           => Module::t('task', 'ATTR__USERS_APPROVE_EXECUTE__LABEL'),
            '_users_approve_execute_response'  => Module::t('task', 'ATTR__USERS_APPROVE_EXECUTE_RESPONSE__LABEL'),

            '_users_performers'                => Module::t('task', 'ATTR__USERS_PERFORMERS__LABEL'),
            '_users_performers_finished'       => Module::t('task', 'ATTR__USERS_PERFORMERS_FINISHED__LABEL'),

            '_users_check_result'              => Module::t('task', 'ATTR__USERS_CHECK_RESULT__LABEL'),
            '_users_check_result_response'     => Module::t('task', 'ATTR__USERS_CHECK_RESULT_RESPONSE__LABEL'),

            '_users_notify_after_finished'     => Module::t('task', 'ATTR__USERS_NOTIFY_AFTER_FINISHED__LABEL'),
            
            '_author'     => Module::t('task', 'ATTR__AUTHOR__LABEL'),

            'created_at' => Module::t('task', 'ATTR__CREATED_AT__LABEL'),
            'updated_at' => Module::t('task', 'ATTR__UPDATED_AT__LABEL'),

        ];
    }


    /**
     * Список приоритетов значение => название
     *
     * @return array
     */
    public static function priorityList()
    {
        return [
            self::PRIORITY__LOW    => Module::t('task', 'PRIORITY__LOW'),
            self::PRIORITY__MEDIUM => Module::t('task', 'PRIORITY__MEDIUM'),
            self::PRIORITY__HIGH   => Module::t('task', 'PRIORITY__HIGH'),
        ];
    }

    public function getPriorityList()
    {
        return self::priorityList();
    }

    public function getPriorityLabel()
    {
        $list = $this->getPriorityList();
        if (isset($list[$this->priority])) {
            return $list[$this->priority];
        }
        return Module::t('task', 'PRIORITY__UNKNOWN');
    }

    /**
     * Список типов сроков выполонения
     *
     * @return array
     */
    public function getDeadlineTypeList()
    {
        return [
            self::DEADLINE_TYPE__ONE_TIME    => Module::t('task', 'DEADLINE_TYPE__ONE_TIME'),
            self::DEADLINE_TYPE__EVERY_DAY   => Module::t('task', 'DEADLINE_TYPE__EVERY_DAY'),
            self::DEADLINE_TYPE__EVERY_WEEK  => Module::t('task', 'DEADLINE_TYPE__EVERY_WEEK'),
            self::DEADLINE_TYPE__EVERY_MONTH => Module::t('task', 'DEADLINE_TYPE__EVERY_MONTH'),
            self::DEADLINE_TYPE__EVERY_DATE  => Module::t('task', 'DEADLINE_TYPE__EVERY_DATE'),
        ];
    }

    /**
     * Дни недели
     *
     * @return array
     */
    public function getDeadlineWeekDaysList()
    {
        return [
            1 => 'Понедельник',
            2 => 'Вторник',
            3 => 'Среда',
            4 => 'Четверг',
            5 => 'Пятница',
        ];
    }

    public function getDeadlineWeekDays()
    {
        $weekDays = $this->getDeadlineWeekDaysList();
        if (is_array($this->deadline_every_week)) {
            return implode(', ', array_map(function($day) use ($weekDays){
                if (isset($weekDays[$day])) {
                    return $weekDays[$day];
                }
                return null;
            }, $this->deadline_every_week));
        }
        return null;
    }


    /**
     * Дни месяца
     *
     * @return array
     */
    public function getDeadlineMonthDaysList()
    {
        $days = range(1,31);
        return array_combine($days, $days);
    }

    /**
     * Список доступных значений для выбора времени
     *
     * @return array в формате ("чч:мм" => "чч:мм")
     */
    public function getTimeList()
    {
        $list = [];
        $hours = range(self::TIME_LIST__HOUR_START, self::TIME_LIST__HOUR_END);
        $minutesStep = self::TIME_LIST__MINUTE_STEP;
        $minutes = range(0, 60 - $minutesStep, $minutesStep);
        foreach ($hours as $hour) {
            foreach ($minutes as $minute) {
                $value = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minute, 2, '0', STR_PAD_LEFT);
                $list[$value] = $value;
            }
        }
        return $list;
    }

    /**
     * Возвращает последнее доступное значение времени в списке getTimeList()
     *
     * @return mixed|null
     */
    public function getTimeEndValue()
    {
        $list = $this->getTimeList();
        $lastKey = end($list);
        if (isset($list[$lastKey])) {
            return $list[$lastKey];
        }
        return null;
    }


    /**
     * Название типа установленного деделайна
     *
     * @return mixed|null
     */
    public function getDeadlineTypeLabel()
    {
        $list = $this->getDeadlineTypeList();
        if (isset($list[$this->deadline_type])) {
            return $list[$this->deadline_type];
        }
        return null;
    }


    public static function statusList($short = false)
    {
        $short = $short === true ? '__SHORT' : '';
        return [
            self::STATUS__APPROVAL_AWAITING      => Module::t('task', 'STATUS__APPROVAL_AWAITING' . $short),
            self::STATUS__APPROVAL_FAILED        => Module::t('task', 'STATUS__APPROVAL_FAILED' . $short),
            self::STATUS__IN_PROGRESS            => Module::t('task', 'STATUS__IN_PROGRESS' . $short),
            self::STATUS__CHECK_RESULTS_AWAITING => Module::t('task', 'STATUS__CHECK_RESULTS_AWAITING' . $short),
            self::STATUS__CHECK_RESULTS_FAILED   => Module::t('task', 'STATUS__CHECK_RESULTS_FAILED' . $short),
            self::STATUS__DONE                   => Module::t('task', 'STATUS__DONE' . $short),
        ];
    }

    public function getStatusList($short = false)
    {
        return self::statusList($short);
    }

    public function getStatusLabel($short = false)
    {
        $list = $this->getStatusList($short);
        if (isset($list[$this->status])) {
            return $list[$this->status];
        }
        return Module::t('task', 'STATUS__UNKNOWN');
    }


    public function getStatusLabelShort()
    {
        return $this->getStatusLabel(true);
    }






    public function beforeSave($insert)
    {

        if ($this->isNewRecord) {
            // Если новая модель, то сгенерируем int auto increment ID (для mongodb)
            $this->id = TaskAutoIncrement::getNextAutoIncrementID();
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }


    public function afterValidate()
    {
        if (!$this->hasErrors() && $this->isNewRecord) {
         //   $this->setDeadlines();

        if (!empty($this->_users_approve_execute)) {
            // если есть пользователи которые должны утвердить(разрешить) выполнение задачи
            // установим соответствующий статус "Ожидают утверждения"
            $this->status = self::STATUS__APPROVAL_AWAITING;
            $this->setApprovalDeadline();
        } else {
            // если нет пользователей которые должны утвердить(разрешить) выполнение задачи
            // сразу устнавливам статус "в работе"
            $this->status = self::STATUS__IN_PROGRESS;
        }

        }
        parent::afterValidate(); // TODO: Change the autogenerated stub
    }


		public function validateDates(){

			if ($this->deadline_type == self::DEADLINE_TYPE__ONE_TIME) {
					if (date('d.m.Y') == date('d.m.Y',strtotime($this->perform_date)) && date('G') > date('G',strtotime($this->perform_date)) ) {
						$this->perform_time  = date('H:i').'';
					} else {
						$this->perform_time  = date('H:i',strtotime($this->perform_date));
					}
					$this->perform_timestamp  = strtotime(date('d.m.Y',strtotime($this->perform_date)) . ' ' . $this->perform_time);
					//$this->perform_timestamp  = strtotime($this->perform_date);
					$this->deadline_timestamp  = strtotime($this->deadline_date);

					if($this->perform_timestamp <= time()){
							$this->addError('perform_date','Дата начала не может быть меньше текущей!');
					}

					if($this->deadline_timestamp <= $this->perform_timestamp){
						$this->addError('deadline_date','Дата завершения не может быть меньше даты начала!');
					}
			}
			elseif ($this->deadline_type == self::DEADLINE_TYPE__EVERY_DATE) {
					if (!isset($this->template) || !$this->template ) {
							$day = $this->deadline_every_date[0];
							$this->perform_timestamp = strtotime($this->perform_date);
							$this->deadline_timestamp = strtotime($this->deadline_date);

							if($this->perform_timestamp <= time()){
									$this->addError('perform_date','Дата начала не может быть меньше текущей!');
							}

							if($this->deadline_timestamp <= $this->perform_timestamp){
								$this->addError('deadline_date','Дата завершения не может быть меньше даты начала!');
							}
					}		
			} else {
					if (!isset($this->template) || !$this->template ) {
						//if (date('d.m.Y') == $this->start_date && date('G') > 8) {
						if (date('d.m.Y') == date('d.m.Y',strtotime($this->start_date)) && date('G') > date('G',strtotime($this->start_date)) ) {
							$this->start_time  = date('H:i').'';
						} else {
							$this->start_time  = date('H:i',strtotime($this->start_date)); //self::PERFORM_TIME_DEFAULT;
						}
						$this->start_timestamp  = strtotime(date('d.m.Y',strtotime($this->start_date)) . ' ' . $this->start_time);
						//$this->end_timestamp  = strtotime($this->end_date);
						$this->end_timestamp  = strtotime(date('d.m.Y',strtotime($this->end_date)) . ' 23:59');
						//$this->end_time = date('H:i',strtotime($this->end_date));

						//первый шаг
						$this->perform_timestamp  = $this->start_timestamp;
						//$this->deadline_timestamp  = strtotime(date('d.m.Y',strtotime($this->start_date)) . ' ' . $this->end_time);
						$this->deadline_timestamp  = strtotime($this->deadline_date);

						if($this->start_timestamp <= time()){
							$this->addError('start_date','Дата начала не может быть меньше текущей!');
						}
						
						if($this->end_timestamp <= $this->start_timestamp){
							$this->addError('end_date','Дата завершения цикла не может быть меньше даты начала!');
						}

						if($this->deadline_timestamp <= $this->start_timestamp){
							$this->addError('deadline_date','Дата завершения не может быть меньше даты начала!');
						}

					}
			}

		}

    /**
     * Обрабатывает данные и устанавливает временные метки(timestamp) для различных дедлайнов
     */
    public function setDeadlines()
    {
        $now = $this->getToday();

        // установим timestamp с которого нужно начать выполнение данной задачи
        if (!empty($this->start_date)) {
            $this->start_timestamp = strtotime($this->start_date);
        }

        // установим timestamp до какого момента должны создаваться клоны задач
        // это для циклично выполняющихся задача по определенному периоду - каждый день / каждую неделю / каждый месяц
        if (!empty($this->end_date)) {
            $this->end_timestamp = strtotime($this->end_date . '23:59:59');
        }

				if (!$this->perform_time)
					$this->perform_time  = self::PERFORM_TIME_DEFAULT;

				if (!$this->deadline_time)
					$this->deadline_time = self::DEADLINE_TIME_DEFAULT;

        // в зависимости от типа дедлайна
        // выполнить 1 раз или же выполнять циклично по опредленным интервалам
        // установим дедлайны на выполнение
        switch ($this->deadline_type) {
            case self::DEADLINE_TYPE__ONE_TIME:
                // установим timestamp дедлайны задачи которая выполняется 1 раз

                // начало выполнения
								//if (!$this->perform_timestamp)
								//	$this->perform_timestamp  = strtotime($this->perform_date . ' ' . $this->perform_time);
                // завершение выполнения
                //$this->deadline_timestamp = strtotime($this->deadline_date);
								//$this->deadline_time = date('H:i', $this->deadline_timestamp);

                $this->template = false; // данная задача выполниться только 1 раз, соответственно не может быть шаблоном для клонирования похожих задач
                break;
            case self::DEADLINE_TYPE__EVERY_DAY:
            case self::DEADLINE_TYPE__EVERY_WEEK:
                // если задача выполняется каждый день, то просто помечаем всю неделю [1,2,3,4,5]
                // если же выполняется только по определенным дням недели то берем их из $this->deadline_every_week (устанавливает пользователь через форму)
                $weekDays = $this->deadline_type == self::DEADLINE_TYPE__EVERY_DAY ? [1,2,3,4,5] : $this->deadline_every_week;

                // определение timestamp(даты) с которой будем производить поиск timestamp для дедлайна
                if (empty($this->deadline_timestamp)) {
                    // если timestamp дедлайна ранее не был установлен (например это новая задача)
                    // то начнем поиск с даты начала выполнения задачи + время дедлайна которые установил пользователь
                    // $this->deadline_time нужен только для того что бы метод self::getNextDeadlineInWeek вернул следующий дедлайн с уже установленным временем
                    // Если не брать время установленное юзером в ($this->deadline_time) то вернет 00:00:00
                    $startAt = strtotime($this->start_date . ' ' . $this->deadline_time);
                } else {
                    // если ранее уже был установлен дедлайн задачи (например она уже выполняется или выполнялась ранее)
                    // то начнем поиск следующего дедлайна начиная с текущего(последнего) установленного дедлайна
                    $startAt = $this->deadline_timestamp;
                }

                // Если дедлайн ранее не был установлен (т.е. новая задача), то будем так же учитывать "сегодня"
                // т.е. что бы уже сейчас/сегодня начать выполнение задачи, если вдруг например ближайший дедлайн выпадет на сегодня
                $today = empty($this->deadline_timestamp) ? true : false;

                // определеяем и устанавливаем дедлайн задачи
                $nextDeadline = self::getNextDeadlineInWeek($startAt, $weekDays, $today);
                $nextDeadlineDate = date('d.m.Y', $nextDeadline);
                //$this->perform_timestamp  = strtotime($nextDeadlineDate . ' ' . self::PERFORM_TIME_DEFAULT);
                //$this->deadline_timestamp = strtotime($nextDeadlineDate . ' ' . self::DEADLINE_TIME_DEFAULT);

                $this->template = true; // данная задача выполниться несколько раз, является шаблон для клонирования новых задач
                break;
            case self::DEADLINE_TYPE__EVERY_MONTH:
                // если задача выполняется в определенные даты каждого месяца
                // определение timestamp(даты) с которой будем производить поиск timestamp для дедлайна
                if (empty($this->deadline_timestamp)) {
                    // если timestamp дедлайна ранее не был установлен (например это новая задача)
                    // то начнем поиск с даты начала выполнения задачи + время дедлайна которые установил пользователь
                    // $this->deadline_time нужен только для того что бы метод self::getNextDeadlineInWeek вернул следующий дедлайн с уже установленным временем
                    // Если не брать время установленное юзером в ($this->deadline_time) то вернет 00:00:00
                    $startAt = strtotime(implode(' ', [$this->start_date, $this->deadline_time]));
                } else {
                    // если ранее уже был установлен дедлайн задачи (например она уже выполняется или выполнялась ранее)
                    // то начнем поиск следующего дедлайна начиная с текущего(последнего) установленного дедлайна
                    $startAt = $this->deadline_timestamp;
                }

                // определеяем и устанавливаем дедлайн задачи
                $nextDeadline = self::getNextDeadlineInMonth($startAt, $this->deadline_every_month);
                $nextDeadlineDate = date('d.m.Y', $nextDeadline);
                //$this->perform_timestamp  = strtotime($nextDeadlineDate . ' ' . self::PERFORM_TIME_DEFAULT);
                //$this->deadline_timestamp = strtotime($nextDeadlineDate . ' ' . self::DEADLINE_TIME_DEFAULT);
								//$this->perform_timestamp  = strtotime($this->start_date . ' ' . self::PERFORM_TIME_DEFAULT);
                //$this->deadline_timestamp = strtotime($this->end_date);
                $this->template = true; // данная задача выполниться несколько раз, является шаблон для клонирования новых задач
                break;
            case self::DEADLINE_TYPE__EVERY_DATE:
                // если задача выполняется в определенные даты


                // проверка на то является ли список дат массивом
                if (!is_array($this->deadline_every_date)) {
                    return false;
                }

                $this->template = true; // данная задача выполниться несколько раз, является шаблон для клонирования новых задач

                // превращаем массив дат(хранятся в как строка с значением "хх.хх.хххх") в unix timestamp + время завершения дедлайна($this->deadline_time)
                $deadlineTimestamps = array_map(function($date){
                    return strtotime($date . ' ' . $this->deadline_time);
                }, $this->deadline_every_date);

                // отсортируем в порядке возрастания
                sort($deadlineTimestamps);
								
                if (empty($this->deadline_timestamp)) {
                    // если до этого timestamp не был установлен
                    // устанавливаем самый первый timestamp даты в списке
                    $nextDeadlineDate = $deadlineTimestamps[0];
                } else {
                    $newTimestamp = null;
                    // если ранее уже был установлен timestamp (задача уже выполнялась как минимум 1 раз)
                    foreach ($deadlineTimestamps as $deadlineTimestamp) {
                        // пройдемся по списку timestamps
                        // найдем timestamp из списка который больше последнего deadline_timestamp

                        if ($deadlineTimestamp > $this->deadline_timestamp) {
                            //var_dump(date('d.m.Y H:i', $deadlineTimestamp), date('d.m.Y H:i', $this->deadline_timestamp));
                            // возьмем timestamp в качестве нового дедлайна
                            $newTimestamp = $deadlineTimestamp;
                            break;
                        }
                    }

                    // если ничего не получилось найти
                    if (!$newTimestamp) {
                        return false;
                    }
                    $nextDeadlineDate = $newTimestamp; // устанавливаем новый дедлайн
                }
                //$this->perform_timestamp  = strtotime($nextDeadlineDate . ' ' . self::PERFORM_TIME_DEFAULT);
                //$this->deadline_timestamp = strtotime($nextDeadlineDate . ' ' . self::DEADLINE_TIME_DEFAULT);

                //$this->perform_timestamp  = strtotime(date('d.m.Y',$deadlineTimestamps[0]) . ' ' . self::PERFORM_TIME_DEFAULT);
                //$this->deadline_timestamp = strtotime(date('d.m.Y',$nextDeadlineDate) . ' ' . self::DEADLINE_TIME_DEFAULT);
								//this->start_timestamp = $deadlineTimestamps[0] - (60 * 60 * 24);
                //$this->start_date = date('d.m.Y', $this->start_timestamp);
                //$this->start_time = date('H:i', $this->start_timestamp);

                //$this->end_timestamp = end($deadlineTimestamps);
                //$this->end_date = date('d.m.Y', $this->end_timestamp);
                //$this->end_time = date('H:i', $this->end_timestamp);

                break;
            default:
                return false;
        }


        if (!empty($this->perform_timestamp)) {
            // если timestamp дедлайна был установлен
            // то раскинем по атрибутам данные
            // они по сути ничего не решают
            // просто для того что бы в базе дедлайн хранился еще и в виде даты и времени
           // $this->perform_date = date('d.m.Y', $this->perform_timestamp);
           // $this->perform_time = date('H:i', $this->perform_timestamp);
        }

        if (!empty($this->deadline_timestamp)) {
            // если timestamp дедлайна был установлен
            // то раскинем по атрибутам данные
            // они по сути ничего не решают
            // просто для того что бы в базе дедлайн хранился еще и в виде даты и времени
            //$this->deadline_date = date('d.m.Y', $this->deadline_timestamp);
           // $this->deadline_time = date('H:i', $this->deadline_timestamp);
        }



    }

    public function generateApproveDeadline()
    {

    }







    public function getToday()
    {
        return self::getNow();
    }

    public static function getNow()
    {
        //return strtotime('26.08.2016');
        //return strtotime('25.08.2016 23:59:01');
        //return strtotime('30.08.2016 05:00:01');
        //return strtotime('30.08.2016');
        return strtotime('now');
    }
    
    public function createNextTask()
    {
        if ($this->isNewRecord || $this->template !== true) {
            return false;
        }

        $todayDate = date('d.m.Y', $this->getToday());
        $clone = $this->getClone();
        $clone->validate();

        // будут ли дальше выполнятся клоны
        // если сегодняшняя дата = дате после которой задача не будет клонироваться
        if ($todayDate == $this->end_date) {
            $this->template = false;
            $this->save(false);
        }
        if (!empty($this->_users_approve_execute)) {
            $approveDate = date('d.m.Y', $clone->approve_execute_deadline_timestamp - (60 * 60 * 24));

            if ($approveDate == $todayDate) {
                var_dump($todayDate, $approveDate);
                $cloneExist = self::find()->where([
                    '_parent' => $this->_id,
                    'approve_execute_deadline_timestamp' => $clone->approve_execute_deadline_timestamp,
                    'deadline_timestamp' => $clone->deadline_timestamp,
                ])->exists();
                if (!$cloneExist) {
                    $this->save(false);
                    return $clone->save(false);
                }
            }
        } else {
            $deadlineDate = date('d.m.Y', $clone->deadline_timestamp);
            var_dump($todayDate, $deadlineDate);
            if ($deadlineDate == $todayDate) {
                $cloneExist = self::find()->where([
                    '_parent' => $this->_id,
                    'deadline_timestamp' => $clone->deadline_timestamp,
                ])->exists();
                if (!$cloneExist) {
                    $this->save(false);
                    return $clone->save(false);
                }
            }
        }
    }


    public function getClone()
    {
        $this->template = false; // текущая модель больше не является шаблоном который участвует в выборке для создания следующей аналогичной модели(задачи)

        $clone = new self(); // создаем новую модель
        $clone->setScenario($this->deadline_type); // устанавливаем сценарий, что бы работала валидация
        $clone->attributes = $this->attributes; // клонируем данные

        $clone->deadline_timestamp = $this->deadline_timestamp;
        $clone->approve_execute_deadline_timestamp = $this->approve_execute_deadline_timestamp;
        $clone->template = true; // теперь новая модель будет шаблоном
        $clone->_parent = $this->_id; // устанавливаем родительскую модель
        $clone->_author = $this->_author;
        //$clone->_users_approve_execute_response = []; // Очищачем список тех кто дал разрешение на выполнение, что бы клон задачи ушел на согласование согласование
        //$clone->_users_check_result_response = []; // Очищачем список тех кто проверял старую задачу, что бы в клоне задаче все было чисто (с нуля)
        //$clone->status = !empty($clone->_users_approve_execute) ? self::STATUS__APPROVAL_AWAITING : self::STATUS__IN_PROGRESS;
        return $clone;
    }


    public static function deadlineWeeksListForTimeStr()
    {
        return [
            1 => 'monday',
            2 => 'tuesday',
            3 => 'wednesday',
            4 => 'thursday',
            5 => 'friday',
            6 => 'saturday',
            0 => 'sunday',
        ];
    }

    public static function getDeadlineWeekDayTimeStr($value)
    {
        $list = self::deadlineWeeksListForTimeStr();
        if (isset($list[$value])) {
            return $list[$value];
        }
        return $list[$value];
    }

    public static function getNextDeadlineInWeek($timestamp, $weekDays = [], $currentDay = false, $weekend = false, $works = false)
    {
        $list = [];
        $weekend = CalendarPeriod::listByType(CalendarPeriod::TYPE_HOLIDAYS);
        $works   = CalendarPeriod::listByType(CalendarPeriod::TYPE_WORKDAYS);
        foreach ($weekDays as $weekDay) {
            $nextWeekDayTimestamp = strtotime(self::getDeadlineWeekDayTimeStr($weekDay) . ' ' . date('H:i:s', $timestamp), $timestamp);
            $check = eval('return $nextWeekDayTimestamp <' . ($currentDay === true ? '' : '=') . ' $timestamp;');
            if ($nextWeekDayTimestamp === null || $check) {
                $nextWeekDayTimestamp = strtotime('next ' . self::getDeadlineWeekDayTimeStr($weekDay) . ' ' . date('H:i:s', $timestamp), $timestamp);
            }

            while (true) {
                $nextWeekDayStartTimestamp = strtotime(date('Y-m-d', $nextWeekDayTimestamp));
                $allow = true;
                $forciblyWork = false; // принудительно рабочий день
                if ($weekend) {
                    foreach ($weekend as $weekendDay) {
                        if ($nextWeekDayStartTimestamp == strtotime($weekendDay)) {
                            $allow = false;
                        }
                    }
                }
                if ($works) {
                    foreach ($works as $worksDay) {
                        if ($nextWeekDayStartTimestamp == strtotime($worksDay)) {
                            $allow = true;
                            $forciblyWork = true;
                        }
                    }
                }
                if ($allow) {
                    if (!$forciblyWork) {
                        if (in_array($w = date('w', $nextWeekDayTimestamp),[0,6])) { // проверка на выходной день
                            $nextWeekDayTimestamp += (60 * 60 * 24) * ($w == 0 ? 1 : 2); // если дата выпаодает на выходной то +1(воскресенье) или +2(суббота) дня
                        }
                    }
                    break;
                }
                $nextWeekDayTimestamp = strtotime('+1 day', $nextWeekDayTimestamp);
            }

            $list[] = $nextWeekDayTimestamp;
        }
        sort($list);
        //VarDumper::dump(array_map(function($timestamp){
        //    return date('d.m.Y H:i:s', $timestamp);
        //},$list), 10, true);
        return isset($list[0]) ? $list[0] : false;
    }

    public static function getNextDeadlineInMonth($timestamp, $monthDays = [], $weekend = false, $works = false)
    {
        $list = [];
        $weekend = CalendarPeriod::listByType(CalendarPeriod::TYPE_HOLIDAYS);
        $works   = CalendarPeriod::listByType(CalendarPeriod::TYPE_WORKDAYS);
        foreach ($monthDays as $monthDay) {
            $dateStr = date('Y-m-' . $monthDay . ' H:i:s', $timestamp);
            if(($next = strtotime($dateStr)) < $timestamp) {
                $next = strtotime($dateStr . ' +1month');
            }
            if ($next > $timestamp) {
                while (true) {
                    $nextDayStartTimestamp = strtotime(date('Y-m-d', $next));
                    $allow = true;
                    $forciblyWork = false; // принудительно рабочий день
                    if ($weekend) {
                        foreach ($weekend as $weekendDay) {
                            if ($nextDayStartTimestamp == strtotime($weekendDay)) {
                                $allow = false;
                            }
                        }
                    }
                    if ($works) {
                        foreach ($works as $worksDay) {
                            if ($nextDayStartTimestamp == strtotime($worksDay)) {
                                $allow = true;
                                $forciblyWork = true;
                            }
                        }
                    }
                    if ($allow) {
                        if (!$forciblyWork) {
                            if (in_array($w = date('w', $next),[0,6])) { // проверка на выходной день
                                $next += (60 * 60 * 24) * ($w == 0 ? 1 : 2); // если дата выпаодает на выходной то +1(воскресенье) или +2(суббота) дня
                            }
                        }
                        break;
                    }
                    $next = strtotime('+1 day', $next);
                }
                $list[] = $next;
            }
        }
        sort($list);
        $list = array_unique($list); // могут дублироваться изи за смещения даты по выходному дню
        return isset($list[0]) ? $list[0] : false;
    }






    /**
     * @return TaskQuery
     */
    public static function find()
    {
        return new TaskQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    //public function afterFind()
    //{
    //    parent::afterFind(); // TODO: Change the autogenerated stub
    //    if ($this->deadline_type != self::DEADLINE_TYPE_REPEATS) {
    //        $this->deadlineTimestampFormat = date('d.m.Y', $this->deadline_timestamp);
    //        $this->deadlineTimeFormat = date('h:i', $this->deadline_timestamp);
    //        if (!empty($this->last_deadline_timestamp)) {
    //            $this->lastDeadlineFormat = date('d.m.Y', $this->last_deadline_timestamp);
    //            $this->lastDeadlineTimeFormat = date('h:i', $this->last_deadline_timestamp);
    //        }
    //    }
    //
    //
    //    return true;
    //}

    //public function beforeSave($insert)
    //{
    //
    //
    //
    //
    //
    //    if ($this->isNewRecord) {
    //        $this->id = TaskAutoIncrement::getNextAutoIncrementId();
    //        if (count($this->_users_approve_execute) > 0) {
    //            $this->status = self::STATUS_AWAITING_APPROVAL;
    //            $this->setApprovalDeadline();
    //            //TaskLog::createNotify()
    //        } else {
    //            //$this->status = self::STATUS_AWAITING_EXECUTION;
    //            $this->status = self::STATUS_IN_PROGRESS;
    //        }
    //        $this->_author = Yii::$app->getUser()->getId();
    //    }
    //
    //    if (!$this->isNewRecord && $this->_author === Yii::$app->getUser()->getId()) {
    //        //if (in_array($this->status, [self::STATUS_AWAITING_APPROVAL, self::STATUS_DENIED_APPROVAL])) {
    //        //    $this->setAttribute('_users_approve_execute_answers', []);
    //        //    //TaskLog::createNotify($this, TaskLog::NOTIFY_AGAIN_APPROVE);
    //        //    $this->status = self::STATUS_AWAITING_APPROVAL;
    //        //}
    //        //if (count($this->usersApproveExecute) > 0) {
    //        //
    //        //} else {
    //        //    $this->status = self::STATUS_IN_PROGRESS;
    //        //}
    //    }
    //
    //    return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    //}

    public static function getUserModelId($id)
    {
        return User::find()->where(['_id' => $id])->one()->_id;
    }

    /**
     * Устанавливает дату дейдлайна в зависимости от типа дедлайна
     * @return string
     */
    //public function setDeadline($update = false)
    //{
    //    if ($update === true) {
    //        if (!empty($this->lastDeadlineFormat)) {
    //            $this->last_deadline_timestamp = strtotime($this->lastDeadlineFormat . ' ' . $this->lastDeadlineTimeFormat);
    //        } else {
    //            $this->last_deadline_timestamp = null;
    //        }
    //
    //    }
    //
    //    switch ($this->deadline_type) {
    //        case self::DEADLINE_TYPE_ONE_TIME:
    //            $datetimeString = $this->deadlineTimestampFormat . ' ' . $this->deadlineTimeFormat;
    //            if (($deadline = strtotime($datetimeString)) !== false) {
    //                $this->deadline_timestamp = $deadline;
    //            }
    //            break;
    //        case self::DEADLINE_TYPE_EVERY_DAY:
    //            $datetimeString = $this->deadlineTimestampFormat . ' ' . $this->deadlineTimeFormat;
    //            if ($update === true) {
    //                $this->deadline_timestamp = strtotime($datetimeString);
    //            } else {
    //                //$this->deadline_timestamp = strtotime('+ 1day', $this->deadline_timestamp);
    //                $this->_users_approved_finished_perform = [];
    //                $weekDays = [1,2,3,4,5,6,0];
    //                $this->deadline_timestamp = self::getNextDeadlineInWeek($this->deadline_timestamp, $weekDays, $this->isNewRecord || $update);
    //            }
    //            break;
    //        case self::DEADLINE_TYPE_EVERY_WEEK:
    //            $datetimeString = $this->deadlineTimestampFormat . ' ' . $this->deadlineTimeFormat;
    //            if ($update === true) {
    //                $this->deadline_timestamp = strtotime($datetimeString);
    //            } else {
    //                $weekDays = $this->deadline_every_week ? $this->deadline_every_week : [];
    //                $this->deadline_timestamp = self::getNextDeadlineInWeek($this->deadline_timestamp, $weekDays, $this->isNewRecord || $update);
    //            }
    //
    //            break;
    //        case self::DEADLINE_TYPE_EVERY_MONTH:
    //            $currentDeadlineTimestamp = $this->isNewRecord ? strtotime('today ' . $this->deadlineTimeFormat) : $this->deadline_timestamp;
    //            if ($update === true) {
    //                $this->deadline_timestamp = $currentDeadlineTimestamp;
    //            } else {
    //                $this->deadline_timestamp = self::getNextDeadlineInMonth($currentDeadlineTimestamp, $this->deadline_every_month ? $this->deadline_every_month : []);
    //            }
    //
    //
    //            break;
    //        case self::DEADLINE_TYPE_REPEATS:
    //            if ($update === true) {
    //                if (empty($this->deadline_repeats_counter)) {
    //                    $this->deadline_repeats_counter = 0;
    //                }
    //                //if ($this->isNewRecord) {
    //                //    $this->deadline_repeats_counter = 0;
    //                //}
    //            } else {
    //                if ($this->deadline_repeats_counter < $this->deadline_repeats_number) {
    //                    $this->deadline_repeats_counter++;
    //                    if ($this->deadline_repeats_counter == $this->deadline_repeats_number) {
    //                        //TaskLog::createNotify($this, TaskLog::NOTIFY_SET_STATUS_DONE);
    //                        return self::STATUS_DONE;
    //                    }
    //                }
    //            }
    //            break;
    //    }
    //
    //    if ($this->deadline_type === self::DEADLINE_TYPE_ONE_TIME) {
    //        return self::STATUS_DONE;
    //    } else {
    //
    //        if (!empty($this->last_deadline_timestamp)) {
    //            if ($this->deadline_timestamp >= $this->last_deadline_timestamp) {
    //                $this->deadline_timestamp = $this->last_deadline_timestamp;
    //                return self::STATUS_DONE;
    //            }
    //        }
    //
    //
    //
    //        $this->setAttribute('_users_performers_execute', null);
    //        $this->setAttribute('_users_approved_finished_perform', null);
    //
    //
    //        if (count($this->_users_control_results) > 0) {
    //            $this->setAttribute('_users_control_results_answers', null);
    //            $this->setAttribute('deadline_control_results', null);
    //        }
    //
    //        if (count($this->_users_approve_execute) > 0) {
    //            if ($this->isNewRecord) {
    //                TaskLog::createNotify($this, TaskLog::NOTIFY_TASK_AWAITING_APPROVAL);
    //            } else {
    //                TaskLog::createNotify($this, TaskLog::NOTIFY_AGAIN_APPROVE);
    //            }
    //
    //            $this->setAttribute('_users_approve_execute_answers', null);
    //            $this->setApprovalDeadline();
    //            return self::STATUS_AWAITING_APPROVAL;
    //        } else {
    //            TaskLog::createNotify($this, TaskLog::NOTIFY_SET_STATUS_IN_PROGRESS);
    //            //return self::STATUS_AWAITING_EXECUTION;
    //
    //            return self::STATUS_IN_PROGRESS;
    //        }
    //    }
    //
    //}
    //
    public function setApprovalDeadline()
    {
        //$timestamp = strtotime(self::DEADLINE_APPROVAL__PARAMS);
        $now = self::getNow();

        if ($this->perform_date == date('d.m.Y', $now)) {
            $this->approve_execute_deadline_timestamp = strtotime('+4hours', $this->perform_timestamp);
        } else {
            $timestamp = strtotime('+1day', $now);
			//$timestamp = strtotime('+4hours', $now);

            $weekDay = date('w', $timestamp);
            if ($weekDay == 6) {
                $timestamp += (60 * 60 * 24) * 2;
            } elseif($weekDay == 6) {
                $timestamp += 60 * 60 * 24;
            }
            $this->approve_execute_deadline_timestamp = $timestamp;
        }

				if ($this->approve_execute_deadline_timestamp > $this->deadline_timestamp) $this->approve_execute_deadline_timestamp = $this->deadline_timestamp;
        return true;
    }
    //
    public function setControlResultsDeadline()
    {
        $timestamp = strtotime(self::DEADLINE_CONTROL_RESULTS__PARAMS, self::getNow());
        $weekDay = date('w', $timestamp);

        if ($weekDay == 6) {
            $timestamp += (60 * 60 * 24) * 2;
        } elseif($weekDay == 6) {
            $timestamp += 60 * 60 * 24;
        }

        $this->check_results_deadline_timestamp = $timestamp;
        return true;
    }


    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['_id' => '_author']);
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getUsersApproveExecute()
    {
        return $this->hasMany(User::className(), ['_id' => '_users_approve_execute']);
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getUsersPerformers()
    {
        return $this->hasMany(User::className(), ['_id' => '_users_performers']);
    }


    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getUsersCheckResult()
    {
        return $this->hasMany(User::className(), ['_id' => '_users_check_result']);
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getUsersNotifyAfterFinished()
    {
        return $this->hasMany(User::className(), ['_id' => '_users_notify_after_finished']);
    }



    //public static function priorityLabel($value)
    //{
    //    $list = self::priorityList();
    //    if (isset($list[$value])) {
    //        return $list[$value];
    //    }
    //    return Module::t('task', 'PRIORITY__UNKNOWN');
    //}


    /**
     * Проверяет есть ли ответ от пользователя по определенному атрибуту
     *
     * @param string $usersAttr
     * @param User $user
     * @return null|boolean
     */
    public function getUserAnswerByAttribute($usersAttr, $user)
    {

        //if (property_exists($this, $usersAttr)) {

            if (isset($this->{$usersAttr}[(string)$user->_id])) {
                return $this->{$usersAttr}[(string)$user->_id];
            } else {
                return null;
            }
        //} else {
        //    var_dump(property_exists($this, $usersAttr));
        //}
        //return null;
    }

    public static function usersList($withoutAuthor = false)
    {
        $list = [];
        foreach (User::find()->all() as $model) {

            //if ($withoutAuthor === true) {
            //    $author = !empty($this->_author) ? (string)$this->_author : Yii::$app->getUser()->getId();
            //    if ((string)$model->_id == $author) {
            //        continue;
            //    }
            //}

            $list[(string)$model->_id] = $model->name . (!empty($model->position) ? ' (' . $model->position . ')' : '');
        }
        return $list;
    }

    public function getUsersList($withoutAuthor = false)
    {
        $list = [];
        foreach (User::find()->all() as $model) {

            if ($withoutAuthor === true) {
                $author = !empty($this->_author) ? (string)$this->_author : Yii::$app->getUser()->getId();
                if ((string)$model->_id == $author && Yii::$app->getUser()->getIdentity()->nickname != 'root') {
                    continue;
                }
            }

            $list[(string)$model->_id] = $model->name . (!empty($model->position) ? ' (' . $model->position . ')' : '');
        }
        return $list;
    }



    public function createUserActionUrl($actionType)
    {
        return ['/todo/task/user-action', 'id' => (string) $this->_id, 'type' => $actionType];
    }


    public function getUserActionByType($type)
    {
        $actions = $this->getUserActions();
        if (isset($actions[$type])) {
            return $actions[$type];
        }
        return null;
    }

    public function getUserActions()
    {
        //$actions = [];
        $that = $this;
        $me = Yii::$app->getUser()->getId();
        $actions = [
            // approve start - согласовать, одобрить к выполнению
            'approve_execute' => [
                'label' => 'Акцептировать (разрешить выполнение)',
                'available' => call_user_func(function() use ($that, $me) {
                    // данная задача ждет согласования?
                    $check = $that->status === self::STATUS__APPROVAL_AWAITING || $that->status === self::STATUS__APPROVAL_FAILED;
                    // Присутствую ли я в списке тех кто должен согласовать выполнение
                    $check = $check && in_array($me, $that->_users_approve_execute);
                    // Если я ранее не давал ответ(подтвердил/отклонил) или же НЕ ДАЛ согласие
                    $check = $check && (!isset($that->_users_approve_execute_response[$me]) || $that->_users_approve_execute_response[$me] === false);
                    return $check;
                }),
                'action' => function() use ($that, $me) {
                    $response = $that->_users_approve_execute_response;
                    $response[$me] = true; // добавлю себя в список тех подтвердил
                    $that->setAttribute('_users_approve_execute_response', $response);
                    TaskLog::createNotify($this, TaskLog::NOTIFY_USER_APPROVED_TASK, self::getUserModelId($me));

                    // флешка для того что бы в actionView показать что типа задача выполнена
                    Yii::$app->getSession()->setFlash(TaskLog::NOTIFY_SET_STATUS_DONE, true);

                    // Если уже все юзеры которые должны были подтвердить дали свой ответ
                    if (count($that->_users_approve_execute) === count($that->_users_approve_execute_response)) {
                        $approved = true;
                        // Пройдемся по все кто дал ответ
                        foreach ($that->_users_approve_execute_response as $res) {
                            $approved = $approved && $res;
                        }
                        // если все подтвердили выполнение задачи
                        if ($approved === true) {
                            // установим статус "ожидает подтверждения начал выполнения от исполнителей"
                            //$that->status = self::STATUS_AWAITING_EXECUTION;
                            $that->status = self::STATUS__IN_PROGRESS;
                            TaskLog::createNotify($this, TaskLog::NOTIFY_SET_STATUS_IN_PROGRESS);
							
							//ищем все дочерние задачи для акцепта
							$subtasks = Task::find()->where(['_parent' => $that->_id])->all();
							foreach ($subtasks as $subtask) {
								$subtask->status = self::STATUS__IN_PROGRESS;
								$subtask->save(false);
							}
				
                        }

                        // если хотя бы 1 не дал свое согласие не выполнение задачи
                        if ($approved === false) {
                            // установим статус "задача не прошла согласование"
                            TaskLog::createNotify($this, TaskLog::NOTIFY_TASK_DENIED_APPROVAL);
                            $that->status = self::STATUS__APPROVAL_FAILED;
                        }
                    }
                    return $that->save(false);
                },
                'url' => $that->createUserActionUrl('approve_execute'),
                'renderBtn' => function($action) {
                    return  Html::a($action['label'], $action['url'], ['class' => 'btn btn-primary']);
                },
            ],

            'reject_execute' => [
                'label' => 'Отказать (запретить выполнение)',
                'available' => call_user_func(function() use ($that, $me) {
                    // данная задача ждет согласования?
                    $check = $that->status === self::STATUS__APPROVAL_AWAITING;
                    // Присутствую ли я в списке тех кто должен согласовать выполнение
                    $check = $check && in_array($me, $that->_users_approve_execute);
                    // Если я ранее не давал ответ(подтвердил/отклонил) или же ДАЛ согласие
                    $check = $check && (!isset($that->_users_approve_execute_response[$me]) || $that->_users_approve_execute_response[$me] === true);
                    return $check;
                }),
                'action' => function() use ($that, $me) {
                    $response = $that->_users_approve_execute_response;
                    $response[$me] = false; // добавлю себя в список тех кто подтвердил
                    $that->setAttribute('_users_approve_execute_response', $response);
                    //TaskLog::createNotify($this, TaskLog::NOTIFY_USER_DISAPPROVED_TASK, self::getUserModelId($me));

                    // флешка для того что бы в actionView показать что типа задача выполнена
                    Yii::$app->getSession()->setFlash(TaskLog::NOTIFY_SET_STATUS_DONE, true);

                    $model = new TaskLog();
                    //$model->setScenario(TaskLog::SCE);
                    if ($model->load(Yii::$app->request->post()) ) {
                        $model->task_id = $that->_id;
                        $model->_user = Yii::$app->getUser()->getIdentity()->_id;
                        $model->type = TaskLog::NOTIFY_USER_DISAPPROVED_TASK;
                        $model->attachedFilesUpload = UploadedFile::getInstances($model, 'attachedFilesUpload');
                        $model->uploadAttachedFiles();
                        //if (!empty($model->comment)) {
                            $model->save(false);
                        //}
                        //var_dump($model->save(false));
                        //$model->save(false);
                    }


                    // Если уже все юзеры которые должны были подтвердить дали свой ответ
                    if (count($that->_users_approve_execute) === count($that->_users_approve_execute_response)) {
                        $approved = true;
                        // Пройдемся по все кто дал ответ
                        foreach ($that->_users_approve_execute_response as $res) {
                            $approved = $approved && $res;
                        }
                        // если все подтвердили выполнение задачи
                        if ($approved === true) {
                            // установим статус "ожидает подтверждения начал выполнения от исполнителей"
                            //$that->status = self::STATUS_AWAITING_EXECUTION;
                            $that->status = self::STATUS__IN_PROGRESS;
                            TaskLog::createNotify($this, TaskLog::NOTIFY_SET_STATUS_IN_PROGRESS);
							
							//ищем все дочерние задачи для акцепта
							$subtasks = Task::find()->where(['_parent' => $that->_id])->all();
							foreach ($subtasks as $subtask) {
								$subtask->status = self::STATUS__IN_PROGRESS;
								$subtask->save(false);
							}
				
                        }

                        // если хотя бы 1 не дал свое согласие не выполнение задачи
                        if ($approved === false) {
                            // установим статус "задача не прошла согласование"
                            TaskLog::createNotify($this, TaskLog::NOTIFY_TASK_DENIED_APPROVAL);
                            $that->status = self::STATUS__APPROVAL_FAILED;
                        }
                    }
                    return $that->save(false);
                },
                'url' => $that->createUserActionUrl('reject_execute'),
                'renderBtn' => function($action) {
                    $id = '#' . TaskLog::NOTIFY_USER_DISAPPROVED_TASK . '-modal';
                    return  Html::a($action['label'], $id, [
                        'class' => 'btn btn-primary',
                        'data-toggle' => 'modal',
                        'data-target' => $id
                    ]);
                },
                'renderHtml' => function($action) use ($that) {
                    return Yii::$app->controller->renderPartial('log/' . TaskLog::NOTIFY_USER_DISAPPROVED_TASK . '-modal', [
                        'model' => new TaskLog(['scenario' => TaskLog::SCENARIO_DEFAULT]),
                        'task_id' => $that->getId(),
                        'action' => $action['url'],
                    ]);
                },
            ],

            'again_approve_execute' => [
                'label' => 'Отправить задачу на повторную акцептацию',
                'available' => call_user_func(function() use ($that, $me) {
                    // данная задача ждет согласования?
                    $check = $that->status === self::STATUS__APPROVAL_FAILED;
                    // Я являюсь автором данной задачи?
                    $check = $check && $me == $that->_author;
                    // Количество тех кто должен дать согласение = количеству тех кто дал свой ответ(согласовал или же наоборот не согласовал)
                    $check = $check && (count($that->_users_approve_execute) == count($that->_users_approve_execute_response));
                    return $check;
                }),
                'action' => function() use ($that, $me) {
                    $that->setAttribute('_users_approve_execute_response', null); // обнуляем массив тех кто дал свой ответ по согласованию
                    // установим статус "ожидает подтверждения согласования"
                    $that->status = self::STATUS__APPROVAL_AWAITING;
                    $that->setApprovalDeadline();
                    TaskLog::createNotify($this, TaskLog::NOTIFY_AGAIN_APPROVE);
                    return $that->save(false);
                },
                'url' => $that->createUserActionUrl('again_approve_execute'),
                'renderBtn' => function($action) {
                    return  Html::a($action['label'], $action['url'], ['class' => 'btn btn-primary']);
                },
            ],

            //'started_perform' => [
            //    'label' => 'Приступить к выполнению задачи',
            //    'available' => call_user_func(function() use ($that, $me) {
            //        // данная задача ждет начала выполнения
            //        $check = $that->status === self::STATUS_AWAITING_EXECUTION;
            //        // Присутствую ли я в списке исполнителей и должен подтвердить начало выполнения
            //        $check = $check && in_array($me, $that->_users_performers);
            //        // Если я ранее не давал ответ(подтвердил/отклонил) или же не приступал
            //        $check = $check && (!isset($that->_users_performers_execute[$me]) || $that->_users_performers_execute[$me] === false);
            //        return $check;
            //    }, $that),
            //    'action' => function($model) use ($that, $me) {
            //        $answers = $that->_users_performers_execute;
            //        $answers[$me] = true; // добавлю себя в список тех подтвердил начало выполнения
            //        $that->setAttribute('_users_performers_execute', $answers);
            //        TaskLog::createNotify($this, TaskLog::NOTIFY_USER_STARTED_PERFORM, self::getUserModelId($me));
            //        // Если уже все юзеры которые должны были подтвердить выполнение дали свой ответ
            //        if (count($that->_users_performers) === count($that->_users_performers_execute)) {
            //            $approved = true;
            //            // Пройдемся по все кто дал ответ
            //            foreach ($that->_users_performers_execute as $answer) {
            //                $approved = $approved && $answer;
            //            }
            //            // если все подтвердили начало выполнения задачи
            //            if ($approved === true) {
            //                // установим статус "выполняет"
            //                TaskLog::createNotify($this, TaskLog::NOTIFY_SET_STATUS_IN_PROGRESS);
            //                $that->status = self::STATUS_IN_PROGRESS;
            //            }
            //        }
            //        return $that->save(false);
            //    },
            //    'url' => $that->createUserActionUrl('started_perform'),
            //    'renderBtn' => function($action) {
            //        return  Html::a($action['label'], $action['url'], ['class' => 'btn btn-primary']);
            //    },
            //],

            'finished_perform' => [
                'label' => 'Завершить выполнение' . (count($that->_users_check_result) ? ' и отправить на акцептацию результата' : ''),
                'available' => call_user_func(function() use ($that, $me) {
                    // задача сейчас на выполнении
                    //return true;
                    $check = $that->status === self::STATUS__IN_PROGRESS;
                    // если есть контролирующие выполнение юзеры, то будем искать среди них, если нет то среди исполнителей
                    //$awaitingAnswersUsersAttr = count($that->_users_control_execution) > 0 ? '_users_control_execution' : '_users_performers';
                    $check = $check && in_array($me, $that->_users_performers);
                    $check = $check && (!isset($that->_users_performers_finished[$me]) || $that->_users_performers_finished[$me] === false);
                    return $check;
                }, $that),
                'action' => function() use ($that, $me) {
                    //$awaitingAnswersUsersAttr = count($that->_users_control_execution) > 0 ? '_users_control_execution' : '_users_performers';

                    $response = $that->_users_performers_finished;
                    $response[$me] = true; // добавлю себя в список тех кто завершил выполнение
                    $that->setAttribute('_users_performers_finished', $response);
                    //if ($awaitingAnswersUsersAttr == '_users_performers') {
                    //    //TaskLog::createNotify($this, TaskLog::NOTIFY_USER_FINISHED_PERFORM, self::getUserModelId($me));

                        $model = new TaskLog();
                        //$model->setScenario(TaskLog::SCE);
                        if ($model->load(Yii::$app->request->post()) ) {
                            $model->task_id = $that->_id;
                            $model->_user = Yii::$app->getUser()->getIdentity()->_id;
                            $model->type = TaskLog::NOTIFY_USER_FINISHED_PERFORM;
                            $model->attachedFilesUpload = UploadedFile::getInstances($model, 'attachedFilesUpload');
                            $model->uploadAttachedFiles();
                            //if (!empty($model->comment)) {
                            $model->save(false);
                            //}
                            //var_dump($model->save(false));
                            //$model->save(false);
                        }
                    //
                    //} else {
                    //    TaskLog::createNotify($this, TaskLog::NOTIFY_USER_FINISHED_PERFORM_CONTROL, self::getUserModelId($me));
                    //}

                    // флешка для того что бы в actionView показать что типа задача выполнена
                    Yii::$app->getSession()->setFlash(TaskLog::NOTIFY_SET_STATUS_DONE, true);

                    // Если уже все юзеры которые должны были подтвердить окончание выполнения дали свои ответы
                    if (count($that->_users_performers) === count($that->_users_performers_finished)) {
                        $approved = true;
                        // Пройдемся по все кто дал ответ
                        foreach ($that->_users_performers_finished as $res) {
                            $approved = $approved && $res;
                        }
                        // если все дали утвердительный ответ
                        if ($approved === true) {
                            // Если есть юзеры которые должны проконтролировать результат
                            if (count($that->_users_check_result) > 0) {
                                // установим статус контроля результата
                                TaskLog::createNotify($this, TaskLog::NOTIFY_SET_STATUS_AWAITING_CHECK_RESULTS);
                                $that->status = self::STATUS__CHECK_RESULTS_AWAITING;
                                $that->setControlResultsDeadline();
                            } else {
                                // или задача выполнена
                                TaskLog::createNotify($this, TaskLog::NOTIFY_SET_STATUS_DONE);
                                Yii::$app->getSession()->setFlash(TaskLog::NOTIFY_SET_STATUS_DONE, true); // флешка для того что бы сделать редирект в контроллере
                                $that->status = self::STATUS__DONE;
                                //$that->status = $that->setDeadline();
                            }
                        }
                    }

                    return $that->save(false);
                },
                'url' => $that->createUserActionUrl('finished_perform'),
                //'renderBtn' => function($action) {
                //    return  Html::a($action['label'], $action['url'], ['class' => 'btn btn-primary']);
                //},
                'renderBtn' => function($action) {
                    $id = '#' . TaskLog::NOTIFY_USER_FINISHED_PERFORM . '-modal';
                    return  Html::a($action['label'], $id, [
                        'class' => 'btn btn-primary',
                        'data-toggle' => 'modal',
                        'data-target' => $id
                    ]);
                },
                'renderHtml' => function($action) use ($that) {
                    return Yii::$app->controller->renderPartial('log/' . TaskLog::NOTIFY_USER_FINISHED_PERFORM . '-modal', [
                        'model' => new TaskLog(['scenario' => TaskLog::SCENARIO_DEFAULT]),
                        'task_id' => $that->getId(),
                        'action' => $action['url'],
                    ]);
                },
            ],

            'back_perform' => [
                'label' => 'Продолжить выполнение задачи',
                'available' => call_user_func(function() use ($that, $me) {
                    // задача сейчас на выполнении или не прошла проверку результата
                    $check = $that->status === self::STATUS__IN_PROGRESS;
                    // если есть контролирующие выполнение юзеры, то будем искать среди них, если нет то среди исполнителей
                    $check = $check && in_array($me, $that->_users_performers);
                    $check = $check && (isset($that->_users_performers_finished[$me]));
                    return $check;
                }, $that),
                'action' => function() use ($that, $me) {
                    //$awaitingAnswersUsersAttr = count($that->_users_control_execution) > 0 ? '_users_control_execution' : '_users_performers';
                    $finished = $that->_users_performers_finished;
                    unset($finished[$me]);
                    $that->setAttribute('_users_performers_finished', $finished);
                    //if ($awaitingAnswersUsersAttr == '_users_performers') {
                        TaskLog::createNotify($this, TaskLog::NOTIFY_USER_BACK_PERFORM, self::getUserModelId($me));
                    //} else {
                    //    TaskLog::createNotify($this, TaskLog::NOTIFY_USER_BACK_PERFORM_CONTROL, self::getUserModelId($me));
                    //}

                        // Если уже все юзеры которые должны были подтвердить окончание выполнения отсутствуют(вернулись к выполненению)
                    //if (count($that->_users_approved_finished_perform) === 0) {
                    //    // сделаем задачу снова выполняемм
                    //    if ($that->status !== self::STATUS_IN_PROGRESS) {
                    //        TaskLog::createNotify($this, TaskLog::NOTIFY_SET_STATUS_IN_PROGRESS);
                    //    }
                    //    $that->status = self::STATUS_IN_PROGRESS;
                    //    // очистим список юзеров которые до этого проверяли результат,
                    //    // для того что бы отправить задачу на повторную проверку результата,
                    //    // после того как юзеры опять подтвердят что задача готова и её можно проверят
                    //    $that->setAttribute('_users_control_results_answers', null);
                    //}

                    return $that->save(false);
                },
                'url' => $that->createUserActionUrl('back_perform'),
                'renderBtn' => function($action) {
                    return  Html::a($action['label'], $action['url'], ['class' => 'btn btn-primary']);
                },
            ],

            'approve_results' => [
                'label' => 'Акцептировать результат',
                'available' => call_user_func(function() use ($that, $me) {
                    // данная задача ждет подтверждения(принятия) STATUS_AWAITING_CHECK_RESULTS
                    //var_dump($that->status === self::STATUS_AWAITING_CHECK_RESULTS);
                    $check = $that->status === self::STATUS__CHECK_RESULTS_AWAITING;
                    // Присутствую ли я в списке исполнителей и должен подтвердить начало выполнения
                    $check = $check && in_array($me, $that->_users_check_result);
                    // Если я ранее не давал ответ(подтвердил/отклонил) или не давал ответ
                    $check = $check && (!isset($that->_users_check_result_response[$me]) || $that->_users_check_result_response[$me] === false);
                    return $check;
                }, $that),
                'action' => function() use ($that, $me) {
                    $response = $that->_users_check_result_response;
                    $response[$me] = true; // добавлю себя в список тех подтвердил начало выполнения
                    $that->setAttribute('_users_check_result_response', $response);
                    TaskLog::createNotify($this, TaskLog::NOTIFY_USER_APPROVED_RESULTS, self::getUserModelId($me));

                    // флешка для того что бы в actionView показать что типа задача выполнена
                    Yii::$app->getSession()->setFlash(TaskLog::NOTIFY_SET_STATUS_DONE, true);

                    // Если уже все юзеры которые должны были подтвердить выполнение дали свой ответ
                    if (count($that->_users_check_result) === count($that->_users_check_result_response)) {
                        $approved = true;
                        // Пройдемся по все кто дал ответ
                        foreach ($that->_users_check_result_response as $res) {
                            $approved = $approved && $res;
                        }
                        // если все приняли результата
                        if ($approved === true) {
                            // установим статус "готово"
                            TaskLog::createNotify($this, TaskLog::NOTIFY_SET_STATUS_DONE);
                            //Yii::$app->getSession()->setFlash(TaskLog::NOTIFY_SET_STATUS_DONE, true);
                            $that->status = self::STATUS__DONE;
                            //$that->status = $that->setDeadline();
                        } else {
                            TaskLog::createNotify($this, TaskLog::NOTIFY_SET_STATUS_DISAPPROVE_RESULTS);
                            $that->status = self::STATUS__CHECK_RESULTS_FAILED;

                            // т.к. задача не прошла контроль результата
                            TaskLog::createNotify($this, TaskLog::NOTIFY_SET_STATUS_IN_PROGRESS);

                            $that->status = self::STATUS__IN_PROGRESS; // установим снова статус в работе
                            $that->setAttribute('_users_performers_finished', null); // очистим список исполнителей который завершили работу, для того что бы у них снова появислась кнопка "Завершить выполнение"
                            $that->setAttribute('_users_check_result_response', null);  // очистим список пользователей которые дали свои ответы, для того что бы в при следующем контроле стали доступны управляющие кнопки что бы снова оценить результат
                        }
                    }

                    return $that->save(false);
                },
                'url' => $that->createUserActionUrl('approve_results'),
                'renderBtn' => function($action) {
                    return  Html::a($action['label'], $action['url'], ['class' => 'btn btn-primary']);
                },
            ],

            'reject_results' => [
                'label' => 'Отклонить результат',
                'available' => call_user_func(function() use ($that, $me) {
                    // данная задача ждет подтверждения(принятия) STATUS_AWAITING_CHECK_RESULTS
                    $check = $that->status === self::STATUS__CHECK_RESULTS_AWAITING;
                    // Присутствую ли я в списке исполнителей и должен подтвердить начало выполнения
                    $check = $check && in_array($me, $that->_users_check_result);
                    // Если я ранее не давал ответ(подтвердил/отклонил) или не давал ответ
                    $check = $check && (!isset($that->_users_check_result_response[$me]) || $that->_users_check_result_response[$me] === true);
                    return $check;
                }, $that),
                'action' => function() use ($that, $me) {
                    $response = $that->_users_check_result_response;
                    $response[$me] = false; // добавлю себя в список тех отверг
                    $that->setAttribute('_users_check_result_response', $response);
                    //TaskLog::createNotify($this, TaskLog::NOTIFY_USER_DISAPPROVED_RESULTS, self::getUserModelId($me));


                    $model = new TaskLog();
                    //$model->setScenario(TaskLog::SCE);
                    if ($model->load(Yii::$app->request->post()) ) {
                        $model->task_id = $that->_id;
                        $model->_user = Yii::$app->getUser()->getIdentity()->_id;
                        $model->type = TaskLog::NOTIFY_USER_DISAPPROVED_RESULTS;
                        $model->attachedFilesUpload = UploadedFile::getInstances($model, 'attachedFilesUpload');
                        $model->uploadAttachedFiles();
                        //if (!empty($model->comment)) {
                            $model->save(false);
                        //}
                        //var_dump($model->save(false));
                        //$model->save(false);
                    }

                    // флешка для того что бы в actionView показать что типа задача выполнена
                    Yii::$app->getSession()->setFlash(TaskLog::NOTIFY_SET_STATUS_DONE, true);

                    $deadline = false;
                    // Если уже все юзеры которые должны были подтвердить выполнение дали свой ответ
                    if (count($that->_users_check_result) === count($that->_users_check_result_response)) {
                        $approved = true;
                        // Пройдемся по все кто дал ответ
                        foreach ($that->_users_check_result_response as $res) {
                            $approved = $approved && $res;
                        }
                        // если все приняли результата
                        if ($approved === true) {
                            // установим статус "готово"
                            TaskLog::createNotify($this, TaskLog::NOTIFY_SET_STATUS_DONE);
                            //Yii::$app->getSession()->setFlash(TaskLog::NOTIFY_SET_STATUS_DONE, true);
                            $that->status = self::STATUS__DONE;
                            //$that->status = $that->setDeadline();
                        } else {
                            TaskLog::createNotify($this, TaskLog::NOTIFY_SET_STATUS_DISAPPROVE_RESULTS);
                            $that->status = self::STATUS__CHECK_RESULTS_FAILED;

                            // т.к. задача не прошла контроль результата
                            TaskLog::createNotify($this, TaskLog::NOTIFY_SET_STATUS_IN_PROGRESS);

                            $that->status = self::STATUS__IN_PROGRESS; // установим снова статус в работе
                            $that->setAttribute('_users_performers_finished', null); // очистим список исполнителей который завершили работу, для того что бы у них снова появислась кнопка "Завершить выполнение"
                            $that->setAttribute('_users_check_result_response', null);  // очистим список пользователей которые дали свои ответы, для того что бы в при следующем контроле стали доступны управляющие кнопки что бы снова оценить результат
                        }
                    }

                    return $that->save(false);
                },
                'url' => $that->createUserActionUrl('reject_results'),
                'renderBtn' => function($action) {
                    $id = '#' . TaskLog::NOTIFY_USER_DISAPPROVED_RESULTS . '-modal';
                    return  Html::a($action['label'], $id, [
                        'class' => 'btn btn-danger',
                        'data-toggle' => 'modal',
                        'data-target' => $id
                    ]);
                },
                'renderHtml' => function($action) use ($that) {
                    return Yii::$app->controller->renderPartial('log/' . TaskLog::NOTIFY_USER_DISAPPROVED_RESULTS . '-modal', [
                        'model' => new TaskLog(['scenario' => TaskLog::SCENARIO_DEFAULT]),
                        'task_id' => $that->getId(),
                        'action' => $action['url'],
                    ]);
                },
            ],

            // approve result - принять результат
            // reject result  - отклонить результат
        ];

        return array_filter($actions, function($action){
            return $action['available'] === true;
        });
    }




    //public static function outboxAwaitingExecutionCount()
    //{
    //    $count = self::find()->outboxAwaitingExecution()->count();
    //    return $count > 0 ? $count : null;
    //}
    //
    //public static function outboxInProgressCount()
    //{
    //    $count = self::find()->outboxInProgress()->count();
    //    return $count > 0 ? $count : null;
    //}
    //
    //public static function outboxControlExecutionCount()
    //{
    //    $count = self::find()->outboxControlExecution()->count();
    //    return $count > 0 ? $count : null;
    //}
    //
    //public static function outboxAwaitingApprovalExecutionCount()
    //{
    //    $count = self::find()->outboxAwaitingApprovalExecution()->count();
    //    return $count > 0 ? $count : null;
    //}
    //
    //public static function outboxAwaitingCheckResultsCount()
    //{
    //    $count = self::find()->outboxAwaitingCheckResults()->count();
    //    return $count > 0 ? $count : null;
    //}
    //
    //public static function outboxDoneCount()
    //{
    //    $count = self::find()->outboxDone()->count();
    //    return $count > 0 ? $count : null;
    //}


    /**
     * Задачи которые ожидают выполнения(ответа) от пользователя
     *
     * В данном списке отображаются задачи
     * - задачи в которых я являюсь исполнителем и которые имею статус self::STATUS__IN_PROGRESS
     * - qwe
     *
     * @return int|null
     */
    public static function inboxAwaitingResponseCount()
    {
        $count = self::find()->inboxAwaitingResponse()->count();
        return $count > 0 ? $count : null;

    }

    public static function inboxOverdueCount()
    {
        $count = self::find()->inboxOverdue()->count();
        return $count > 0 ? $count : null;
    }


    public static function inboxCheckCount()
    {
        $count = self::find()->inboxCheck()->count();
        return $count > 0 ? $count : null;
    }

    public static function inboxDoneCount()
    {
        $count = self::find()->inboxDone()->count();
        return $count > 0 ? $count : null;
    }
    
    
    public static function outboxApprovingCount()
    {
        $count = self::find()->outboxApproving()->count();
        return $count > 0 ? $count : null;
    }

    public static function outboxPerformedCount()
    {
        $count = self::find()->outboxPerformed()->count();
        return $count > 0 ? $count : null;
    }

    public static function outboxExpiredCount()
    {
        $count = self::find()->outboxExpired()->count();
        return $count > 0 ? $count : null;
    }

    public static function outboxDoneCount()
    {
        $count = self::find()->outboxDone()->count();
        return $count > 0 ? $count : null;
    }

    public static function outboxCheckCount()
    {
        $count = self::find()->outboxCheck()->count();
        return $count > 0 ? $count : null;
    }

    /**
     * Загрзука файлов
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function uploadAttachedFiles()
    {
        if ($this->validate(['attachedFilesUpload'])) {
            foreach ($this->attachedFilesUpload as $file) {
                $uploadPath = Module::getInstance()->getProtectedFilesUploadPath(true);

                $fileDateTime = '';
                if(($fileTimestamp = filectime($file->tempName)) !== false) {
                    $fileDateTime = date('Y-m-d_h-i-s') . '_';
                }

                $fileNameOrig = $fileDateTime . $file->name;
                $fileName = Inflector::slug($file->name) . '-'. uniqid() . '.' . $file->extension;
                if ($file->saveAs($uploadPath . $fileName)) {
                    $_attached_files = $this->_attached_files;
                    $_attached_files[] = [
                        'filename_orig' => $fileNameOrig,
                        'filename'      => $fileName,
                    ];
                    $this->setAttribute('_attached_files', $_attached_files);
                }
            }
            return true;
        } else {
            return false;
        }
    }


    /**
     * Список ссылок на файлы
     *
     * Возвращает key=>value массив где key - это название файла, а value url для скачивания(просмотра) файла
     *
     * @return array
     */

    public function getAttachedFilesLinks()
    {
        $list = [];
        if (is_array($this->_attached_files)) {
            foreach ($this->_attached_files as $file) {
                $list[$this->getAttachedFileUrl($file['filename'])] = $file['filename_orig'];
            }
        }
        return $list;
    }

    /**
     * Возвращает ссылку на скачивание файла
     *
     * @param $filename название файла
     * @param bool $scheme если true то ссылка будет абслютной (с http://site.com/)
     * @return string
     */
    protected function getAttachedFileUrl($filename, $scheme = false)
    {
        return Url::to(['/todo/task/download-attached-file', 'id' => (string) $this->_id, 'filename' => $filename], $scheme);
    }

    /**
     * Список файлов и пути где они лежата на хостинге(сервере)
     *
     * Возвращает key=>value массив где key - это название файла, а value абсолютный путь расположения файла в на хостинге(сервере)
     *
     * @return array
     */
    public function getAttachedFilesPaths()
    {
        $list = [];
        if (is_array($this->_attached_files)) {
            foreach ($this->_attached_files as $file) {
                //$list[$filename] = $this->getAttachedFileSystemPath($filename);
                $list[$file['filename']] = [
                    'filename_orig' => $file['filename_orig'],
                    'file_path' => $this->getAttachedFileSystemPath($file['filename']),
                ];
            }
        }
        return $list;
    }

    /**
     * Возвращает путь где находится файла в системе
     *
     * @param $filename имя файла
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    protected function getAttachedFileSystemPath($filename)
    {
        return Module::getInstance()->getProtectedFilesUploadPath(true) . $filename;
    }

    /**
     * Проверяет наличие доступа к задаче для ппросмотра, или совершения других двействий
     *
     * @param mixed $user_id уканикальный id пользователя
     * @return bool
     */
    public function checkAvailableAccess($user_id)
    {

				if (!Yii::$app->getUser()->isGuest && Yii::$app->getUser()->getIdentity()->nickname === 'root') {
            return true;
				}

        if ($user_id == (string) $this->_author) {
            return true;
        }

        // исполнители
        if (in_array($user_id, $this->_users_performers)) {
            return true;
        }

        // согласующие
        if (in_array($user_id, $this->_users_approve_execute)) {
            return true;
        }

        // контроль результата
        if (in_array($user_id, $this->_users_check_result)) {
            return true;
        }

        // получают уведомления
        if (in_array($user_id, $this->_users_notify_after_finished)) {
            return true;
        }

        return false;
    }


    public function getViewRoute()
    {
        return ['/todo/task/view', 'id' => $this->getId()];
    }

    public function getViewUrl($scheme = false)
    {
        return Url::to($this->getViewRoute(), $scheme);
    }

    public function getId()
    {
        return (string)$this->_id;
    }

    public function listenChangeAttributes()
    {
        return [
            'priority',
            '_attached_files',
            'deadline_type',
            'deadline_every_week',
            'deadline_every_month',
            'deadline_every_date',
            '_users_performers',
            '_users_approve_execute',
            '_users_notify_after_finished',
            '_users_check_result',
            'deadline_timestamp',
        ];
    }

    //public static function inboxInProgressCount()
    //{
    //    $count = self::find()->inboxInProgress()->count();
    //    return $count > 0 ? $count : null;
    //}
    //
    //public static function inboxControlExecutionCount()
    //{
    //    $count = self::find()->inboxControlExecution()->count();
    //    return $count > 0 ? $count : null;
    //}
    //
    //public static function inboxAwaitingApprovalExecutionCount()
    //{
    //    $count = self::find()->inboxAwaitingApprovalExecution(true)->count();
    //    return $count > 0 ? $count : null;
    //}
    //
    //public static function inboxAwaitingCheckResultsCount()
    //{
    //    $count = self::find()->inboxAwaitingCheckResults()->count();
    //    return $count > 0 ? $count : null;
    //}
    //
    //public static function inboxDoneCount()
    //{
    //    $count = self::find()->inboxDone()->count();
    //    return $count > 0 ? $count : null;
    //}
}
