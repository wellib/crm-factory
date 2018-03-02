<?php

namespace app\modules\canteen\controllers;

use app\modules\accounts\models\User;
use app\modules\canteen\models\OrderSearch;
use app\modules\canteen\models\ReportForm;
use app\modules\canteen\models\ReportSearch;
use app\modules\canteen\Module;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class ReportController extends Controller
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

    public function actionIndex()
    {
        $userList = $this->getUsers();

        $orderSearch = new OrderSearch();
        $orderSearch->created_at_from = Yii::$app->formatter->asDate(time());
        $orderSearch->created_at_to = Yii::$app->formatter->asDate(time());
        $orderSearch->validate();
        $dataProvider = $orderSearch->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'orderSearch' => $orderSearch,
            'userList' => $userList,
        ]);
    }

    protected function getUsers()
    {
        return User::find()->orderBy(['nickname' => SORT_ASC])->all();
    }
}
