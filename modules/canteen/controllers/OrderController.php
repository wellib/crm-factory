<?php

namespace app\modules\canteen\controllers;

use app\modules\accounts\models\User;
use app\modules\canteen\models\OrderForm;
use app\modules\canteen\models\OrderSearch;
use app\modules\canteen\Module;
use Yii;
use yii\base\Model;
use yii\bootstrap\Html;
use yii\filters\AccessControl;
use yii\web\Controller;

class OrderController extends Controller
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
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionMy()
    {
        $orderSearch = new OrderSearch();
        $orderSearch->created_at_from = Yii::$app->formatter->asDate(time());
        $orderSearch->created_at_to = Yii::$app->formatter->asDate(time());
        $params = Yii::$app->request->get(null, []);
        $params['OrderSearch']['employee_ids'] = [Yii::$app->user->id];

        $dataProvider = $orderSearch->search($params);

        return $this->render('my', [
            'orderSearch' => $orderSearch,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        if (!Yii::$app->getModule('canteen')->canteen->isOpen()) {
            return $this->render('/default/closed');
        }

        $orderForm = new OrderForm();
        $data = Yii::$app->request->post();
        if (!Yii::$app->user->can(Module::ROLE_CANTEEN_ADMIN)) {
            $data['OrderForm']['employee_id'] = Yii::$app->user->id;
        }

        $userList = $this->getUsers();

        if ($orderForm->load($data) && Model::loadMultiple($orderForm->orderDishes, Yii::$app->request->post()) && $orderForm->loadModelAttributes()) {
            $orderForm->order->save(false);

            $success = true;
            foreach ($orderForm->orderDishes as $dish) {
                if ($dish->in_order) {
                    $dish->order_id = (string)$orderForm->order->_id;
                    if ($dish->loadModelAttributes()) {
                        $dish->orderDish->save(false);
                    } else {
                        Yii::$app->session->addFlash('error', Html::errorSummary($dish));
                        $success = false;
                    }
                }
            }

            if ($success) {
                if (Yii::$app->getModule('canteen')->canteen->isOrderOnly()) {
                    Yii::$app->session->addFlash('success', 'Заказ успешно сформирован!');
                    return $this->redirect(['create']);
                } else {
                    return $this->redirect([Yii::$app->user->can(Module::ROLE_CANTEEN_ADMIN) ? 'report/index' : 'my']);
                }
            }
        }

        return $this->render('create', [
            'orderForm' => $orderForm,
            'userList' => $userList,
        ]);
    }

//    public function actionUpdate($id)
//    {
//        $order = Order::findOne($id);
//        $order->employee = 'Сотрудник';
//        $order->save();
//        if ($order === null) {
//            throw new NotFoundHttpException();
//        }
//
//        $userList = $this->getUsers();
//
//        $orderForm = new OrderForm();
//        $orderForm->setOrder($order);
//        $orderForm->loadFormAttributes();
//
//        if ($orderForm->load(Yii::$app->request->post()) && Model::loadMultiple($orderForm->orderDishes, Yii::$app->request->post()) && $orderForm->loadModelAttributes()) {
//            $orderForm->order->save(false);
//
//            $success = true;
//            foreach ($orderForm->orderDishes as $dish) {
//                if ($dish->in_order) {
//                    $dish->order_id = (string)$orderForm->order->_id;
//                    if ($dish->loadModelAttributes()) {
//                        $dish->orderDish->save(false);
//                    } else {
//                        Yii::$app->session->addFlash('error', Html::errorSummary($dish));
//                        $success = false;
//                    }
//                } else {
//                    if (!$dish->orderDish->isNewRecord) {
//                        $dish->orderDish->delete();
//                    }
//                }
//            }
//
//            if ($success) {
//                return $this->redirect(['update', 'id' => (string)$orderForm->order->_id]);
//            }
//        }
//
//        return $this->render('update', [
//            'orderForm' => $orderForm,
//            'userList' => $userList,
//        ]);
//    }
//
//    public function actionDelete($id)
//    {
//        $order = Order::findOne($id);
//        if ($order === null) {
//            throw new NotFoundHttpException();
//        }
//
//        $order->delete();
//
//        return $this->redirect(['index']);
//    }

    protected function getUsers()
    {
        return User::find()->all();
    }
}