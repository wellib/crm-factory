<?php
namespace app\modules\accounts\models;


use app\modules\accounts\Module;
use yii\helpers\Url;

/**
 * Class RecoveryForm
 * 
 * @property string $login e-mail или никнейм
 * @property null|User $_user Модель пользователя
 * @package app\modules\accounts\models
 */
class RecoveryForm extends \yii\base\Model
{
    const FLASH_KEY__SUCCESSFULLY = 'RECOVERYFORM_SUCCESSFULLY';
    
    /**
     * @var string
     */
    public $login;

    /**
     * @var User
     */
    protected $_user = false;

    public function rules()
    {
        return [
            ['login', 'string'],
            ['login', 'required'],
            ['login', 'userExists'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'login' => Module::t('recovery', 'ATTR__LOGIN__LABEL'),
        ];
    }

    /**
     * Проверяет, существует ли модель пользователя, т.е. найден ли пользователь в базе
     * 
     * @param $attribute
     * @param $params
     */
    public function userExists($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user) {
                $this->addError($attribute, Module::t('recovery', 'ATTR__LOGIN__VALIDATE_MESSAGE__USER_NOT_FOUND'));
            }
        }
    }

    public function recovery()
    {
        if ($this->validate()) {
            $this->getUser()->createMailRecoveryRequest()->save(false);
            return true;
        }
        return false;
    }

    /**
     * Поиск пользователя
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::find()->where([
                'email'    => $this->login,
            ])->orWhere([
                'nickname' => $this->login,
            ])->one();
        }
        return $this->_user;
    }


    public function getRecoveryUrl()
    {
        return Url::to(['/accounts/user/change-password', 'email_token' => $this->getUser()->mail_recovery_token], true);
    }

}