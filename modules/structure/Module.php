<?php

namespace app\modules\structure;

use Yii;

/**
 * structure module definition class
 */
class Module extends \yii\base\Module
{
    const ROLE_NAME = 'structure';
    const ROLE_DESCRIPTION = 'Управление структурой';
    
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\structure\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->registerTranslations();

        // только для консоли
        if (Yii::$app instanceof \yii\console\Application) {
            $this->mongodbMigrateCommandsInit();
            $this->customCommandsInit();
        }

        // только для сайта
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
        $this->controllerNamespace = 'app\modules\structure\commands';
    }

    /**
     * Добавляет возможность использования миграций MongoDB в рамках данного модуля
     */
    public function mongodbMigrateCommandsInit()
    {
        $this->controllerMap['mongodb'] = [
            'class' => 'yii\mongodb\console\controllers\MigrateController',
            'migrationCollection' => 'structure_migration',
            'migrationPath' => '@app/modules/structure/mongodb-migrations',
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
            'url' => ['/structure/department/tree'],
            //'active' => true,
            'icon' => 'sitemap',
            'sort' => 999,
            'visible' => Yii::$app->getUser()->can(self::ROLE_NAME),
        ]);
        return true;
    }

    /**
     * Добавляет возможность перевода сообщений в рамках модуля
     */
    public function registerTranslations()
    {
        Yii::$app->i18n->translations['modules/structure/*'] = [
            'class'          => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => Yii::$app->sourceLanguage,
            'basePath'       => '@app/modules/structure/messages',
            'fileMap'        => [
                'modules/structure/module'      => 'module.php',
                'modules/structure/department'  => 'department.php',
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
        return Yii::t('modules/structure/' . $category, $message, $params, $language);
    }

    /**
     * Добавляет роуты
     */
    public function urlManagerRulesInit()
    {
        $rules = [
            'structure/<controller>/<action>' => 'structure/<controller>/<action>',
        ];
        Yii::$app->urlManager->addRules($rules);
    }
}
