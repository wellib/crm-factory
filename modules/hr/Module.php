<?php

namespace app\modules\hr;


use Yii;

/**
 * Отдел кадров
 *
 * Class Module
 * @package app\modules\hr
 */
class Module extends \yii\base\Module
{
    const ROLE_NAME = 'hr';
    const ROLE_DESCRIPTION = 'Отдель кадров';

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\hr\controllers';

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
        }
    }

    /**
     * Делает так что бы при вызове yii в консоли были видны команды модуля в стиле <moduleName>/<commandName>/<commandAction>
     */
    public function customCommandsInit()
    {
        $this->controllerNamespace = 'app\modules\hr\commands';
    }

    /**
     * Добавляет возможность использования миграций MongoDB в рамках данного модуля
     */
    public function mongodbMigrateCommandsInit()
    {
        $this->controllerMap['mongodb'] = [
            'class' => 'yii\mongodb\console\controllers\MigrateController',
            'migrationCollection' => 'hr_migration',
            'migrationPath' => '@app/modules/hr/mongodb-migrations',
        ];
    }

    /**
     * Различные настройки для сайта, например пункты в главном меню
     * @return bool
     */
    public function appInit()
    {
        /* @var $appModule \app\modules\app\Module */
        $appModule = Yii::$app->getModule('app');
        if (!$appModule) {
            return false;
        }

        $appModule->addMenuItem([
            'label' => self::t('module', 'MODULE_NAME'),
            'url' => '#hr',
            //'active' => true,
            'icon' => 'user-circle-o',
            'sort' => 1000,
            'visible' => Yii::$app->getUser()->can(self::ROLE_NAME),
            'items' => [
                [
                    'label' => self::t('employee', 'MODEL_NAME_PLURAL'),
                    'url' => ['/hr/employee/index'],
                    'icon' => 'users',
                ],
                [
                    'label' => self::t('order', 'MODEL_NAME_PLURAL'),
                    'url' => ['/hr/order/index'],
                    'icon' => 'file-text',
                ],
            ],
        ]);
        return true;
    }

    /**
     * Добавляет возможность перевода сообщений в рамках модуля
     */
    public function registerTranslations()
    {
        Yii::$app->i18n->translations['modules/hr/*'] = [
            'class'          => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => Yii::$app->sourceLanguage,
            'basePath'       => '@app/modules/hr/messages',
            'fileMap'        => [
                'modules/hr/module'          => 'module.php',
                'modules/hr/employee'        => 'employee.php',
                'modules/hr/employee-search' => 'employee-search.php',
                'modules/hr/identity-card'   => 'identity-card.php',
                'modules/hr/company-card'    => 'company-card.php',
                'modules/hr/dictionary-word' => 'dictionary-word.php',
                'modules/hr/contact'         => 'contact.php',
                'modules/hr/education'       => 'education.php',
                'modules/hr/family'          => 'family.php',
                'modules/hr/experience'      => 'experience.php',
                'modules/hr/file'            => 'file.php',
                'modules/hr/order'           => 'order.php',
                'modules/hr/hiring'          => 'hiring.php',
                'modules/hr/business-trip'   => 'business-trip.php',
                'modules/hr/fired'           => 'fired.php',
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
        return Yii::t('modules/hr/' . $category, $message, $params, $language);
    }

    /**
     * Добавляет роуты
     */
    public function urlManagerRulesInit()
    {
        $rules = [
            //['class' => 'yii\rest\UrlRule', 'controller' => 'word'],
            'hr/<controller>/<action>' => 'hr/<controller>/<action>',
        ];
        Yii::$app->urlManager->addRules($rules);
    }

}
