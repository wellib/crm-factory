<?php

namespace app\modules\canteen\controllers;

use app\modules\canteen\models\Dish;
use app\modules\canteen\models\DishForm;
use app\modules\canteen\models\DishSearch;
use app\modules\canteen\Module;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DishController extends Controller
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
        $searchModel = new DishSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $dishForm = new DishForm();

        if ($dishForm->load(Yii::$app->request->post()) && $dishForm->loadModelAttributes() && $dishForm->dish->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'dishForm' => $dishForm,
        ]);
    }

    public function actionUpdate($id)
    {
        $dish = Dish::findOne($id);
        if ($dish === null) {
            throw new NotFoundHttpException();
        }

        $dishForm = new DishForm();
        $dishForm->setDish($dish);
        $dishForm->loadFormAttributes();

        if ($dishForm->load(Yii::$app->request->post()) && $dishForm->loadModelAttributes() && $dishForm->dish->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'dishForm' => $dishForm,
        ]);
    }

    public function actionDelete($id)
    {
        $dish = Dish::findOne($id);
        if ($dish === null) {
            throw new NotFoundHttpException();
        }

        $dish->delete();

        return $this->redirect(['index']);
    }
}