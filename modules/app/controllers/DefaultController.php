<?php

namespace app\modules\app\controllers;

use yii\web\Controller;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends Controller
{

    //public function actions()
    //{
    //    return [
    //        'error' => [
    //            'class' => 'yii\web\ErrorAction',
    //        ],
    //    ];
    //}

    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                //'only' => ['create', 'update'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionOffline()
    {
        return $this->render('offline');
    }
}
