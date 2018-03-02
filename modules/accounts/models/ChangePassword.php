<?php
/**
 * Created by PhpStorm.
 * User: stasm
 * Date: 15.07.2016
 * Time: 11:04
 */

namespace app\modules\accounts\models;

use app\modules\accounts\Module;

class ChangePassword extends \yii\base\Model
{
    public $password;
    public $password_repeat;

    public function rules()
    {
        return [
            [['password', 'password_repeat'], 'required'],
            [['password'], 'string', 'min' => User::PASSWORD_MIN_LENGTH],
            [['password_repeat'], 'compare', 'compareAttribute' => 'password'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password' => Module::t('change-password', 'ATTR__PASSWORD__LABEL'),
            'password_repeat' => Module::t('change-password', 'ATTR__PASSWORD_REPEAT__LABEL'),
        ];
    }
}