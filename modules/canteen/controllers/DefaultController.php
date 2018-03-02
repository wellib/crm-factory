<?php

namespace app\modules\canteen\controllers;

use app\modules\canteen\Module;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [Module::ROLE_CANTEEN_ADMIN],
                    ],
                ],
            ],
        ];
    }

    public function actionClose()
    {
        Yii::$app->getModule('canteen')->canteen->close();

        return $this->redirect(['dish/index']);
    }

    public function actionOpen()
    {
        Yii::$app->getModule('canteen')->canteen->open();

        return $this->redirect(['dish/index']);
    }

    public function actionOrderOnly()
    {
        Yii::$app->getModule('canteen')->canteen->modeOrderOnly();

        return $this->redirect(['order/create']);
    }
}