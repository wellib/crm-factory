<?php

namespace app\modules\accounts\controllers;

use app\modules\accounts\models\ChangePassword;
use app\modules\accounts\models\User;
use Yii;
use app\modules\accounts\models\SigninForm;
use app\modules\accounts\models\RecoveryForm;
use yii\web\BadRequestHttpException;

class UserController extends \yii\web\Controller
{
    public function actionSignin()
    {
        $model = new SigninForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['/site/index']);
        }
        return $this->render('signin', [
            'model' => $model,
        ]);
    }

    public function actionSignout()
    {
        Yii::$app->getUser()->logout(true);
        return $this->redirect(['signin']);
    }

    public function actionRecovery()
    {
        $model = new RecoveryForm();
        if ($model->load(Yii::$app->request->post()) && $model->recovery()) {
            $user = $model->getUser();
            $user->sendMail('Восстановление пароля', $this->renderPartial('mail-recovery',[
                'model' => $model,
            ]));
            $model = new RecoveryForm();
            Yii::$app->session->setFlash(RecoveryForm::FLASH_KEY__SUCCESSFULLY, true);
            //return $this->redirect(['/site/index']);
        }
        return $this->render('recovery', [
            'model' => $model,
        ]);
    }

    public function actionChangePassword($email_token)
    {
        $user = User::find()->where(['mail_recovery_token' => $email_token])->one();

        if (!$user) {
            throw new BadRequestHttpException('Возможно ссылка устарела');
        }

        $model = new ChangePassword();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            // do somthing
            $user->mail_recovery_token = null;
            $user->setPassword($model->password)->save(false);
            Yii::$app->user->logout();
            $this->redirect(['signin']);
        }
        return $this->render('change-password', [
            'model' => $model,
        ]);
    }

    public function actionTest()
    {
        //var_dump(Yii::$app->security->generateRandomString(64));
        var_dump(Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['smtpEmail'])
            ->setFrom([Yii::$app->params['smtpEmail'] => Yii::$app->name])
            ->setTo('r@ukrdev.com')
            ->setSubject('Тема сообщения')
            ->setTextBody('Текст сообщения')
            ->setHtmlBody('<b>текст сообщения в формате HTML</b>')
            ->send());
    }
}
