<?php

namespace app\modules\canteen;

use Yii;

class Module extends \yii\base\Module
{
    const ROLE_CANTEEN_ADMIN = 'canteen_admin';
    const ROLE_CANTEEN_ADMIN_DESCRIPTION = 'Администратор столовой';

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\canteen\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Yii::configure($this, require(__DIR__ . '/config.php'));

        if (Yii::$app instanceof \yii\console\Application) {
            $this->initUrlRules();
            $this->initMigrate();
            $this->controllerNamespace = 'app\modules\canteen\commands';
        }

        if (Yii::$app instanceof \yii\web\Application) {
            $this->initUrlRules();
            $this->initMenu();
        }
    }

    public function initUrlRules()
    {
        $rules = [
            'canteen/<controller>/<action>' => 'canteen/<controller>/<action>',
        ];
        Yii::$app->urlManager->addRules($rules);
    }

    public function initMigrate()
    {
        $this->controllerMap['migrate'] = [
            'class' => 'yii\mongodb\console\controllers\MigrateController',
            'migrationCollection' => 'todo_migration',
            'migrationPath' => '@app/modules/canteen/migrations',
        ];
    }

    public function initMenu()
    {
        /* @var $appModule \app\modules\app\Module */
        $appModule = Yii::$app->getModule('app');
        if (!$appModule) {
            return false;
        }

        //добавим пукнт меню в админку
        $appModule->addMenuItem([
            'label' => 'Столовая',
            'url' => '#canteen',
            'controllerId' => 'task',
            'icon' => 'cutlery',
            'sort' => 1,

            'items' => [
                [
                    'label' => 'Меню на неделю',
                    'url' => ['/canteen/dish/index'],
                    'icon' => 'book',
                    'visible' => Yii::$app->user->can(Module::ROLE_CANTEEN_ADMIN) && !Yii::$app->session->get('modeOrderOnly', false),
                ],
                [
                    'label' => 'Мои заказы',
                    'url' => ['/canteen/order/my'],
                    'icon' => 'shopping-cart',
                    'visible' => !Yii::$app->user->isGuest && !Yii::$app->session->get('modeOrderOnly', false),
                ],
                [
                    'label' => 'Новый заказ',
                    'url' => ['/canteen/order/create'],
                    'icon' => 'hand-pointer-o',
                    'visible' => !Yii::$app->user->isGuest,
                ],
                [
                    'label' => 'Отчет',
                    'url' => ['/canteen/report/index'],
                    'icon' => 'table',
                    'visible' => Yii::$app->user->can(Module::ROLE_CANTEEN_ADMIN) && !Yii::$app->session->get('modeOrderOnly', false),
                ],
            ],
        ]);

        return true;
    }

    public function isOpen()
    {

    }
}
