<?php

namespace app\modules\docs;

use Yii;
use yii\base\InvalidConfigException;
use app\modules\docs\models\Task;

/**
 * docs module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\docs\controllers';

    /**
     * @var string Путь для загрузки изображений в системе, можно указать либо абсолюный путь системы, либо yii alias
     */
    public $uploadPath = '@webroot/uploads/docs';

    /**
     * @var string Url куда обращаться для http запроса изображений, можно использовать как абслютный(например с http://cdn.domain.com/images) так и yii alias
     */
    public $uploadUrl = '@web/uploads/docs';

    /**
     * @var string Префикс для названия файла изобржения, используется для уникализации имени если изображения лежат в общей папке с другими изображения.
     */
    public $uploadFilePrefix = '';

    /**
     * @var string Путь для загрузки защищенных файлов, можно указать либо абсолюный путь системы, либо yii alias
     */
    public $protectedFilesUploadPath = '@app/modules/docs/uploads';


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();


        // только для консоли
        if (Yii::$app instanceof \yii\console\Application) {
            $this->mongodbMigrateCommandsInit();
            $this->customCommandsInit();
        }

        // только для сайта
        $this->registerTranslations();
        if (Yii::$app instanceof \yii\web\Application) {
            $this->urlManagerRulesInit();
            $this->appInit();

            if (YII_DEBUG === true) {
                if (!is_dir($this->getUploadPath())) {
                    throw new InvalidConfigException(Module::t('module', 'INVALID_CONFIG_MESSAGE__UPLOAD_DIRECTORY_IS_NOT_EXIST'));
                }
            }

        }
    }

    /**
     * Делает так что бы при вызове yii в консоли были видны команды модуля в стиле <moduleName>/<commandName>/<commandAction>
     */
    public function customCommandsInit()
    {
        $this->controllerNamespace = 'app\modules\docs\commands';
    }

    /**
     * Добавляет возможность использования миграций MongoDB в рамках данного модуля
     */
    public function mongodbMigrateCommandsInit()
    {
        $this->controllerMap['mongodb'] = [
            'class' => 'yii\mongodb\console\controllers\MigrateController',
            'migrationCollection' => 'docs_migration',
            'migrationPath' => '@app/modules/docs/mongodb-migrations',
        ];
    }


    /**
     * Различные настройки для админки, например пункты меню и controllerMap
     * @return bool
     */
    public function adminInit()
    {
        /* @var $adminModule \app\modules\admin\Module */
        //$adminModule = Yii::$app->getModule('admin');
        //if (!$adminModule) {
        //    return false;
        //}

        //добавим контроллер в админку
        //$adminModule->addControllerMap('accounts-user', [
        //    'class' => 'app\modules\accounts\controllers\backend\UserController',
        //    'viewPath' => '@app/modules/accounts/views/backend/user',
        //]);
        //$adminModule->addControllerMap('accounts', [
        //    'class' => 'app\modules\accounts\controllers\UserController',
        //    'viewPath' => '@app/modules/accounts/views/user',
        //]);
        //
        //
        ////добавим пукнт меню в админку
        //$adminModule->addMenuItem([
        //    'label' => self::t('user', 'MODEL_NAME'),
        //    'url' => ['/admin/accounts-user/index'],
        //    'controllerId' => 'accounts-user',
        //    'icon' => 'user',
        //    'sort' => 1000,
        //]);
        ////var_dump('admin-menu-init');
        //
        ////добавим контроллер в админку
        //$adminModule->addControllerMap('user', [
        //    'class' => 'app\modules\accounts\controllers\UserController',
        //    'viewPath' => '@app/modules/accounts/views/user',
        //]);
    }

    /**
     * Различные настройки для сайта, например пункты в главном меню
     * @return bool
     */
    public function appInit()
    {
        /* @var $adminModule \app\modules\app\Module */
        $appModule = Yii::$app->getModule('app');
        if (!$appModule) {
            return false;
        }

        if (!Yii::$app->getUser()->isGuest && Yii::$app->getUser()->getIdentity()->nickname === 'root') {
          $appModule->addMenuItem([
              'label' => 'Панель управления',
              'url' => '#docs',
              'controllerId' => 'task',
              'icon' => 'building-o',
              'sort' => 10000,
              'items' => [
                              [
                                  'label' => 'Структура',
                                  'url' => ['/docs/company/index'],
                                  'icon' => 'building-o',
                                  'sort' => 1000,
                                  //'visible' => !Yii::$app->getUser()->isGuest && Yii::$app->getUser()->getIdentity()->nickname === 'root',
                              ],
                              [
                                  'label' => 'Сотрудники',
                                  'url' => ['/accounts/user/index'],
                                  'controllerId' => 'user',
                                  'icon' => 'user',
                                  'sort' => 2000,
                              ],
                              [
                                  'label' => 'Календарные периоды',
                                  'url' => ['/todo/calendar-period/index'],
                                  'icon' => 'calendar',
                                  //'visible' => !Yii::$app->getUser()->isGuest && Yii::$app->getUser()->getIdentity()->nickname === 'root',
                              ],
                              [
                                  'label' => 'Пользователи',
                                  'url' => '#users',
                                  'icon' => 'users',
                                  'sort' => 3000,
                                  'items' => [
                                      [
                                          'label' => 'Профили',
                                          'url' => ['/accounts/user/index'],
                                          'icon' => 'user',
                                          'sort' => 1000,
                                      ],
                                      [
                                          'label' => 'Структура',
                                          'url' => ['/accounts/structure/index'],
                                          'icon' => 'sitemap',
                                          'sort' => 2000,
                                      ],
                                      [
                                          'label' => 'История',
                                          'url' => ['/accounts/history/index'],
                                          'icon' => 'eye',
                                          'sort' => 3000,
                                      ],
                                  ],
                              ],
                          ],
          ]);
        }

        ////добавим пукнт меню в админку
        $appModule->addMenuItem([
            'label' => self::t('module', 'Ежедневник'),
            'url' => '/docs/calendar/index',
            'controllerId' => 'calendar',
            'icon' => 'calendar',
            'sort' => 8000,
        ]);

        $counters = [
            'inbox' => [
                'awaiting-response' => Task::inboxAwaitingResponseCount(),
                'overdue'           => Task::inboxOverdueCount(),
                'check'             => Task::inboxCheckCount(),
                'done'              => Task::inboxDoneCount(),
            ],
            'outbox' => [
                'approving' => Task::outboxApprovingCount(),
                'performed' => Task::outboxPerformedCount(),
                'expired'   => Task::outboxExpiredCount(),
                'done'      => Task::outboxDoneCount(),
                'check'     => Task::outboxCheckCount(),
            ],
        ];
        //
        //
        //
        //
        //$allCount    = 0;
        //$inboxCount  = 0;
        //$outboxCount = 0;
        //foreach ($counters as $key => $tasksCounters) {
        //    foreach ($tasksCounters as $type => $tasksCounter) {
        //        if ($type == 'done') {
        //            continue;
        //        }
        //        $allCount += $tasksCounter;
        //        if ($key == 'inbox') {
        //            $inboxCount += $tasksCounter;
        //        }
        //        if ($key == 'outbox') {
        //            $outboxCount += $tasksCounter;
        //        }
        //    }
        //}
        //
        //$allCount    = $allCount    > 0 ? $allCount : '';
        //$inboxCount  = $inboxCount  > 0 ? $inboxCount : '';
        //$outboxCount = $outboxCount > 0 ? $outboxCount : '';
        //
        ////добавим пукнт меню в админку
        $appModule->addMenuItem([
            'label' => self::t('module', 'MODULE_NAME'),
            //'url' => ['/docs/task/index'],
            'url' => '#docs',
            'controllerId' => 'task',
            'icon' => 'file-text',
            'sort' => 6000,
            //'badge' => $allCount,
            //'badgeOptions' => ['class'=>'label-info'],

            //'multipleBadges' => [
            //    [
            //        'label' => (($c = $counters['inbox']['awaiting-response'] + $counters['outbox']['performed']) > 0) ? $c : null,
            //        'options' => ['class'=>'label-success']
            //    ],
            //    [
            //        'label' => (($c = $counters['inbox']['overdue'] + $counters['outbox']['expired']) > 0) ? $c : null,
            //        'options' => ['class'=>'label-danger']
            //    ],
            //    [
            //        'label' => (($c = $counters['inbox']['check'] + $counters['outbox']['approving']) > 0) ? $c : null,
            //        'options' => ['class'=>'label-warning']
            //    ],
            //    //[
            //    //    'label' => $counters['outbox']['check'],
            //    //    'options' => ['class'=>'label-info']
            //    //],
            //],


            'items' => [
                [
                    'label' => 'Входящие',
                    'url' => ['/docs/task/index-inbox'],
                    'icon' => 'inbox',
                    //'visible' => !Yii::$app->getUser()->isGuest && Yii::$app->getUser()->getIdentity()->nickname === 'root',
                ],

                [
                    'label' => 'Исходящие',
                    'url' => ['/docs/task/index-outbox'],
                    'icon' => 'share',
                ],
                //[
                //    'label' => Module::t('calendar_period', 'MODEL_NAME_PLURAL'),
                //    'url' => ['/docs/calendar-period/index'],
                //    'icon' => 'calendar',
                //    'visible' => !Yii::$app->getUser()->isGuest && Yii::$app->getUser()->getIdentity()->nickname === 'root',
                //],
                //[
                //    'label' => 'Создать задачу',
                //    'url' => ['/docs/task/create'],
                //    'icon' => 'plus',
                //],
                [
                    'label' => 'Служебные записки',
                    'url' => '#qwe',
                    'icon' => 'file-text-o',
                    'items' => [
                        [
                            'label' => self::t('task', 'INBOX'),
                            //'badge' => $inboxCount,
                            //'badgeOptions' => ['class'=>'label-info'],
                            'url' => '#inbox',
                            //'icon' => 'inbox',
                            'multipleBadges' => [
                                [
                                    'label' => $counters['inbox']['awaiting-response'],
                                    'options' => ['class'=>'label-success']
                                ],
                                [
                                    'label' => $counters['inbox']['overdue'],
                                    'options' => ['class'=>'label-danger']
                                ],
                                [
                                    'label' => $counters['inbox']['check'],
                                    'options' => ['class'=>'label-warning']
                                ],
                                //[
                                //    'label' => $counters['outbox']['check'],
                                //    'options' => ['class'=>'label-info']
                                //],
                            ],

                            'items' => [
                                [
                                    'label' => self::t('task', 'INBOX_IN_PROGRESS'),
                                    //'url' => '#tasks-inbox-performance',
                                    'url' => ['/docs/task/inbox-awaiting-response'],
                                    //'badge' => Task::inboxAwaitingResponseCount(),
                                    'badge' => $counters['inbox']['awaiting-response'],
                                    'badgeOptions' => ['class'=>'label-success'],
                                ],
                                [
                                    'label' => self::t('task', 'INBOX_OVERDUE'),
                                    //'url' => '#tasks-inbox-overdue',
                                    'url' => ['/docs/task/inbox-overdue'],
                                    //'badge' => Task::inboxOverdueCount(),
                                    'badge' => $counters['inbox']['overdue'],
                                    'badgeOptions' => ['class'=>'label-danger'],
                                ],
                                [
                                    'label' => self::t('task', 'INBOX_CHECK'),
                                    //'url' => '#tasks-inbox-check',
                                    'url' => ['/docs/task/inbox-check'],
                                    //'badge' => Task::inboxCheckCount(),
                                    'badge' => $counters['inbox']['check'],
                                    'badgeOptions' => ['class'=>'label-warning'],
                                ],
                                [
                                    'label' => self::t('task', 'INBOX_DONE'),
                                    //'url' => '#tasks-inbox-done',
                                    'url' => ['/docs/task/inbox-done'],
                                    //'badge' => Task::inboxDoneCount(),
                                    'badge' => $counters['inbox']['done'],
                                    'badgeOptions' => ['class'=>'label-default'],
                                ],
                            ],
                        ],
                        [
                            'label' => self::t('task', 'OUTBOX'),
                            'url' => '#outbox',
                            //'icon' => 'share',
                            //'badge' => $outboxCount,
                            //'badgeOptions' => ['class'=>'label-info'],
                            'multipleBadges' => [
                                [
                                    'label' => $counters['outbox']['approving'],
                                    'options' => ['class'=>'label-warning']
                                ],
                                [
                                    'label' => $counters['outbox']['performed'],
                                    'options' => ['class'=>'label-success']
                                ],
                                [
                                    'label' => $counters['outbox']['expired'],
                                    'options' => ['class'=>'label-danger']
                                ],
                                //[
                                //    'label' => $counters['outbox']['check'],
                                //    'options' => ['class'=>'label-info']
                                //],
                            ],
                            'items' => [
                                [
                                    'label' => self::t('task', 'OUTBOX_APPROVING'),
                                    //'url' => '#tasks-outbox-approving',
                                    'url' => ['/docs/task/outbox-approving'],
                                    //'badge' => Task::outboxApprovingCount(),
                                    'badge' => $counters['outbox']['approving'],
                                    'badgeOptions' => ['class'=>'label-warning'],
                                ],
                                [
                                    'label' => self::t('task', 'OUTBOX_PERFORMED'),
                                    //'url' => '#tasks-outbox-performed',
                                    'url' => ['/docs/task/outbox-performed'],
                                    //'badge' => Task::outboxPerformedCount(),
                                    'badge' => $counters['outbox']['performed'],
                                    'badgeOptions' => ['class'=>'label-success'],
                                ],
                                [
                                    'label' => self::t('task', 'OUTBOX_EXPIRED'),
                                    //'url' => '#tasks-outbox-expired',
                                    'url' => ['/docs/task/outbox-expired'],
                                    //'badge' => Task::outboxExpiredCount(),
                                    'badge' => $counters['outbox']['expired'],
                                    'badgeOptions' => ['class'=>'label-danger'],
                                ],
                                [
                                    'label' => self::t('task', 'OUTBOX_DONE'),
                                    //'url' => '#tasks-outbox-done',
                                    'url' => ['/docs/task/outbox-done'],
                                    //'badge' => Task::outboxDoneCount(),
                                    'badge' => $counters['outbox']['done'],
                                    'badgeOptions' => ['class'=>'label-default'],
                                ],
                                //[
                                //    'label' => self::t('task', 'OUTBOX_CHECK'),
                                //    //'url' => '#tasks-outbox-check',
                                //    'url' => ['/docs/task/outbox-check'],
                                //    //'badge' => Task::outboxCheckCount(),
                                //    'badge' => $counters['outbox']['check'],
                                //    'badgeOptions' => ['class'=>'label-warning'],
                                //],
                            ],
                        ],
                    ]
                ]

            ],
        ]);

        ////добавим пукнт меню в админку
        $appModule->addMenuItem([
            'label' => self::t('module', 'Реестр договоров'),
            'url' => '/docs/contract/index',
            'controllerId' => 'contract',
            'icon' => 'file-archive-o ',
            'sort' => 3000,
        ]);
        $appModule->addMenuItem([
            'label' => self::t('module', 'Шаблоны документов'),
            'url' => '/docs/template/index',
            'controllerId' => 'template',
            'icon' => 'file-code-o',
            'sort' => 3000,
        ]);
        return true;
    }

    /**
     * Добавляет возможность перевода сообщений в рамках модуля
     */
    public function registerTranslations()
    {
        Yii::$app->i18n->translations['modules/docs/*'] = [
            'class'          => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => Yii::$app->sourceLanguage,
            'basePath'       => '@app/modules/docs/messages',
            'fileMap'        => [
                'modules/docs/module' => 'module.php',
                'modules/docs/task'   => 'task.php',
                'modules/docs/task_log'   => 'task_log.php',
                //'modules/docs/calendar_period'   => 'calendar_period.php',
                //'modules/docs/signin' => 'signin.php',
            ],
        ];
    }

    /**
     * Кастомный метод для первода сообщений внутри модуля
     *
     * @param $category
     * @param $message
     * @param array $params
     * @param null $language
     *
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('modules/docs/' . $category, $message, $params, $language);
    }

    /**
     * Добавляет роуты
     */
    public function urlManagerRulesInit()
    {
        $rules = [
            //'docs/task/view/<id>' => 'docs/task/view',
            //'docs/task/download-attached-file/<id>/<filename>' => 'docs/task/download-attached-file',
            'docs/<controller>/<action>' => 'docs/<controller>/<action>',
            //'signin' => 'admin/accounts/signin',
        ];
        Yii::$app->urlManager->addRules($rules);
    }

    /**
     * @param bool $trailingDirectorySeparator Добавить в конец системны разделитель каталогов
     * @return bool|string
     */
    public function getUploadPath($trailingDirectorySeparator = false)
    {
        //if (YII_DEBUG === true) {
        //    FileHelper::
        //}
        if (!($path = Yii::getAlias($this->uploadPath))) {
            throw new InvalidConfigException('qweqwe');
        }
        return $path . ($trailingDirectorySeparator ? DIRECTORY_SEPARATOR : '');
    }

    /**
     * @return bool|string
     */
    /**
     * @param bool $trailingSlash Добавить слэш в конец
     * @return string
     */
    public function getUploadUrl($trailingSlash = false)
    {
        return Yii::getAlias($this->uploadUrl) . ($trailingSlash ? '/' : '');
    }

    /**
     * @return bool|string
     */
    public function getUploadFilePrefix()
    {
        return $this->uploadFilePrefix;
    }

    /**
     * Возращает путь в системе для загрузки защищенных файлов
     *
     * @param bool $trailingDirectorySeparator Знак "/"(unix) или "\"(windows) в конце пути
     * @return string
     * @throws InvalidConfigException
     */
    public function getProtectedFilesUploadPath($trailingDirectorySeparator = false)
    {
        if (!($path = Yii::getAlias($this->protectedFilesUploadPath))) {
            throw new InvalidConfigException('Не корректно указан путь для защищенных файлов');
        }
        return $path . ($trailingDirectorySeparator ? DIRECTORY_SEPARATOR : '');
    }
}
