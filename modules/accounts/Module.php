<?php

namespace app\modules\accounts;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;

/**
 * @property string $controllerNamespace
 * @property integer $sessionDuration
 * @property string $uploadPath
 * @property string $uploadUrl
 * @property string $uploadFilePrefix
 * 
 * accounts module definition class
 */
class Module extends \yii\base\Module implements \yii\base\BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\accounts\controllers';

    /**
     * Время жизни сессия после авторизации, если юзер используется галочку "запомнить меня"
     * @var int
     */
    public $sessionDuration = 2592000; // 3600*24*30
    
    /**
     * @var string Путь для загрузки изображений в системе, можно указать либо абсолюный путь системы, либо yii alias
     */
    public $uploadPath = '@webroot/uploads/accounts';

    /**
     * @var string Url куда обращаться для http запроса изображений, можно использовать как абслютный(например с http://cdn.domain.com/images) так и yii alias
     */
    public $uploadUrl = '@web/uploads/accounts';

    /**
     * @var string Префикс для названия файла изобржения, используется для уникализации имени если изображения лежат в общей папке с другими изображения.
     */
    public $uploadFilePrefix = 'account_';

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
            $this->registerTranslations();
        }
        // только для web
        if (Yii::$app instanceof \yii\web\Application) {
            $this->registerTranslations();
            $this->urlManagerRulesInit();
            $this->appInit();

            if (YII_DEBUG === true) {
                if (!is_dir($this->getUploadPath())) {
                    throw new InvalidConfigException(Module::t('module', 'INVALID_CONFIG_MESSAGE__UPLOAD_DIRECTORY_IS_NOT_EXIST'));
                }
            }

        }
    }
    
    public function bootstrap($app)
    {
        // TODO: Implement bootstrap() method.
    }

    /**
     * Делает так что бы при вызове yii в консоли были видны команды модуля в стиле <moduleName>/<commandName>/<commandAction>
     */
    public function customCommandsInit()
    {
        $this->controllerNamespace = 'app\modules\accounts\commands';
    }

    /**
     * Добавляет возможность использования миграций MongoDB в рамках данного модуля
     */
    public function mongodbMigrateCommandsInit()
    {
        $this->controllerMap['mongodb-migrate'] = [
            'class' => 'yii\mongodb\console\controllers\MigrateController',
            'migrationCollection' => 'accounts_migration',
            'migrationPath' => '@app/modules/accounts/mongodb-migrations',
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
        //
        ////добавим контроллер в админку
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

        if (false && !Yii::$app->getUser()->isGuest && Yii::$app->getUser()->getIdentity()->nickname === 'root') {
            //добавим пукнт меню в админку
            $appModule->addMenuItem([
                'label' => self::t('user', 'MODEL_NAME'),
                'url' => ['/accounts/user/index'],
                'controllerId' => 'user',
                'icon' => 'user',
                'sort' => 1000,
            ]);
        }


        return true;
    }

    /**
     * Добавляет возможность перевода сообщений в рамках модуля
     */
    public function registerTranslations()
    {
        Yii::$app->i18n->translations['modules/accounts/*'] = [
            'class'          => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => Yii::$app->sourceLanguage,
            'basePath'       => '@app/modules/accounts/messages',
            'fileMap'        => [
                'modules/accounts/module' => 'module.php',
                'modules/accounts/user'   => 'user.php',
                'modules/accounts/signin' => 'signin.php',
                'modules/accounts/recovery' => 'recovery.php',
                'modules/accounts/change-password' => 'change-password.php',
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
        return Yii::t('modules/accounts/' . $category, $message, $params, $language);
    }

    /**
     * Добавляет роуты
     */
    public function urlManagerRulesInit()
    {
        $rules = [
            //'accounts' => 'accounts/user/index',
            //'accounts/user/test' => 'accounts/user/test',
            'accounts/<controller>/<action>' => 'accounts/backend/<controller>/<action>',
            'signin' => 'accounts/user/signin',
            'signout' => 'accounts/user/signout',
            'restore' => 'accounts/user/restore',
            'recovery' => 'accounts/user/recovery',
            'change-password' => 'accounts/user/change-password',
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

}
