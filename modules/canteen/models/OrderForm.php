<?php
namespace app\modules\canteen\models;

use MongoDB\BSON\ObjectID;
use Yii;
use yii\base\Model;
use yii\bootstrap\Html;

/**
 * @property Order $order
 */
class OrderForm extends Model
{
    public $employee_id;

    /**
     * @var OrderDishForm[]
     */
    public $orderDishes = [];

    /**
     * @var Order
     */
    private $_order;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['employee_id', 'required'],

            ['orderDishes', 'required'],
            [
                'orderDishes',
                function ($attribute) {
                    $exist = false;
                    foreach ($this->orderDishes as $orderDish) {
                        if ($orderDish->in_order) {
                            $exist = true;
                            break;
                        }
                    }
                    if (!$exist) {
                        $this->addError($attribute, 'Необходимо выбрать минимум одно блюдо');
                    }
                }
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        /** @var Dish[] $dishes */
        $dishes = Dish::find()->where(['week_day' => (int)date('N')])->all();
        foreach ($dishes as $dish) {
            $orderDishForm = new OrderDishForm();
            $orderDish = new OrderDish();

            $orderDish->dish_id = $dish->_id;
            $orderDish->name = $dish->name;
            $orderDish->price = $dish->price;
            $orderDish->portion = $dish->portion;

            $orderDishForm->setOrderDish($orderDish);
            $orderDishForm->loadFormAttributes();
            $this->orderDishes[(string)$dish->_id] = $orderDishForm;
        }

        $this->loadFormAttributes();
    }

    public function loadModelAttributes()
    {
        $isValid = $this->validate();

        if ($isValid) {
            $this->order->employee_id = $this->employee_id;

            $isValid = $this->order->validate() && $isValid;
            if (!$isValid) {
                Yii::$app->session->addFlash('error', Html::errorSummary($this->order));
            }
        } else {
            Yii::$app->session->addFlash('error', Html::errorSummary($this));
        }

        return $isValid;
    }

    public function loadFormAttributes()
    {
        foreach ($this->orderDishes as $key => $orderDishForm) {
            // Если заказы не новый, то загружаем параметры заказа
            if (!$this->order->isNewRecord) {
                $orderDish = OrderDish::findOne(['dish_id' => new ObjectID($key), 'order_id' => $this->order->_id]);
                if ($orderDish) {
                    $orderDishForm->setOrderDish($orderDish);
                    $orderDishForm->loadFormAttributes();

                    $orderDishForm->quantity = $orderDish->quantity;
                    $orderDishForm->in_order = true;
                }
            } else {
                $orderDishForm->loadFormAttributes();
            }
        }

        $this->employee_id = $this->order->employee_id;
    }

    public function setOrder(Order $order)
    {
        $this->_order = $order;
    }

    public function getOrder()
    {
        if ($this->_order === null) {
            $this->_order = new Order();
        }

        return $this->_order;
    }

    public function attributeLabels()
    {
        return $this->order->attributeLabels();
    }
}