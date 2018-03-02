<?php

namespace app\modules\accounts\models;

use Yii;
use app\modules\accounts\Module;

/**
 *
 * @property string $login
 * @property string $password
 * @property boolean $rememberMe
 *
 * SigninForm is the model behind the login form.
 */
class SigninForm extends \yii\base\Model
{
    public $login;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // login and password are both required
            [['login', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'login'      => Module::t('signin', 'ATTRIBUTE__LOGIN__LABEL'),
            'password'   => Module::t('signin', 'ATTRIBUTE__PASSWORD__LABEL'),
            'rememberMe' => Module::t('signin', 'ATTRIBUTE__REMEMBER_ME__LABEL'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Module::t('signin', 'VALIDATE_MESSAGE__INCORRECT_LOGIN_OR_PASSWORD'));
            }
        }
    }

    /**
     * Logs in a user using the provided login and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            $module = Module::getInstance(); /* @var $module \app\modules\accounts\Module */
            return Yii::$app->user->login($user, $this->rememberMe ? $module->sessionDuration : 0);
        }
        return false;
    }

    /**
     * Finds user by [[login]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::find()->findByLogin($this->login)->one();
        }
        return $this->_user;
    }
}
