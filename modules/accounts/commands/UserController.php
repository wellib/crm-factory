<?php

namespace app\modules\accounts\commands;

use Yii;

use yii\helpers\Console;

use app\modules\accounts\Module;
use app\modules\accounts\models\User;

class UserController extends \yii\console\Controller
{
    /**
     * Create user
     */
    public function actionCreate()
    {
        $name = $this->prompt('Name:', ['required' => true]);
        $nickname = $this->prompt('Nickname:', ['required' => true]);
        $email = $this->prompt('E-mail:', ['required' => true, 'validator' => function($input, &$error) {
            if (!filter_var($input, FILTER_VALIDATE_EMAIL)) {
                $error = 'Must be valid email address!';
                return false;
            }
            return true;
        }]);
        $password = $this->prompt('Password:', ['required' => true, 'validator' => function($input, &$error) {
            if (strlen($input) < 6) {
                $error = 'Min length 6 characters!';
                return false;
            }
            return true;
        }]);

        $model = new User();
        $model->setScenario(User::SCENARIO_CREATE);
        $model->setAttributes([
            'name'     => $name,
            'nickname' => $nickname,
            'email'    => $email,
            'password' => $password,
        ]);
        if (!$model->validate()) {
            foreach ($model->getErrors() as $attribute => $errors) {
                foreach ($errors as $error) {
                    echo $this->ansiFormat($error, Console::FG_RED);
                }
            }
            return false;
        }
        if ($model->save(false)) {
            echo $this->ansiFormat('User successfully created!', Console::FG_GREEN);
        } else {
            echo $this->ansiFormat('Save model failed: something wrong, method "save" return false result!', Console::FG_GREEN);
        }
    }
}
