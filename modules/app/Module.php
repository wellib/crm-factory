<?php

namespace app\modules\app;

use Yii;
use yii\helpers\Url;

/**
 * app module definition class
 *
 * @property string $layout
 * @property array $_navGroups
 *
 * @package app\modules\app
 */
class Module extends \yii\base\Module
{
    const DEFAULT_GROUP_KEY = 'general';

    //public $layout = 'main';

    private $_navGroups  = [
        'general' => [],
    ];

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\app\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Yii::setAlias('@admin', '@app/modules/admin');

        if (Yii::$app instanceof \yii\web\Application) {
            $this->urlManagerRulesInit();
        }
    }


    /**
     * Добавляет контроллеры в модуль
     * Используется для того что бы другие модули могли добалять свои контроллеры для управления своим модулем
     *
     * @param string $controllerName Название контроллера
     * @param array $config Конфиг контроллера
     *
     * @return bool
     */
    public function addControllerMap($controllerName, $config)
    {
        if (isset($this->controllerMap[$controllerName])) {
            return false;
        }
        $this->controllerMap[$controllerName] = $config;
        return true;
    }


    /**
     * Добавление новой группы меню
     *
     * @param string $key Уникальный ключ
     * @param string $label Название
     * @param null $sort Позиция в списке
     * @return bool Возвращает true если группа успешно добавлена или false если такая группа уже существует и повторно не может быть добавлена
     */
    public function addNavGroup($key, $label, $sort = null)
    {
        if (isset($this->_navGroups[$key])) {
            return false;
        }
        $this->_navGroups[$key] = [
            'label' => $label,
            'sort'  => $sort,
            'items' => [],
        ];
        return true;
    }


    /**
     * Добавляет новый пукт меню в определенную группу
     *
     * @param array $item Массив которй может включать в себя следующие данные http://www.yiiframework.com/doc-2.0/yii-widgets-menu.html#$items-detail
     * @param string $groupKey Ключ группа в которую будет добавлен пункт меню
     * @return bool Если группа в которую пытаются добавить новый пункт не существует то вернет false
     */
    public function addMenuItem($item, $groupKey = self::DEFAULT_GROUP_KEY)
    {
        if (!isset($this->_navGroups[$groupKey])) {
            return false;
        }
        $this->_navGroups[$groupKey][] = $item;
        return true;
    }

    /**
     * Возвращает массив групп
     * @return array
     */
    public function getNavGroups()
    {
        $groups = $this->_navGroups;
        $defaultSortValue = count($groups);
        $sort = [];
        foreach ($groups['general'] as $key => $group) {
            if (isset($group['sort'])) {
                $sort[$key] = $group['sort'];
            } else {
                $sort[$key] = $defaultSortValue;
                $defaultSortValue--;
            }
        }

        array_multisort($sort, SORT_DESC, $groups['general']);
        return $groups;
    }


    public static function homeRoute()
    {
        return ['/app/default/index'];
    }

    public static function homeUrl()
    {
        return Url::to(self::homeRoute());
    }

    /**
     * Добавляет роуты
     */
    public function urlManagerRulesInit()
    {
        //var_dump(11111);
        $rules = [
            'app/<controller>/<action>' => 'app/<controller>/<action>',
            'app/<controller>' => 'app/<controller>/index',
            'app' => 'app/default/index',
            '' => 'app/default/index',
        ];

        Yii::$app->urlManager->addRules($rules);
    }
}
