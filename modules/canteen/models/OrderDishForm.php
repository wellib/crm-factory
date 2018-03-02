<?php
namespace app\modules\canteen\models;

use Yii;
use yii\base\Model;
use yii\bootstrap\Html;

/**
 * @property OrderDish $orderDish
 */
class OrderDishForm extends Model
{
    public $id;
    public $order_id;
    public $dish_id;
    public $name;
    public $week_day;
    public $quantity;
    public $portion;
    public $price;
    public $in_order;

    /**
     * @var OrderDish
     */
    private $_orderDish;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = $this->orderDish->rules();

        $rules[] = ['in_order', 'boolean'];

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->quantity = 1;
    }

    public function loadModelAttributes()
    {
        if ($this->validate()) {
            $this->orderDish->order_id = $this->order_id;
            $this->orderDish->dish_id = $this->dish_id;
            $this->orderDish->name = $this->name;
            $this->orderDish->price = $this->price;
            $this->orderDish->quantity = $this->quantity;
            $this->orderDish->portion = $this->portion;

            $isValid = $this->orderDish->validate();
            if (!$isValid) {
                Yii::$app->session->addFlash('error', Html::errorSummary($this->orderDish));
            }

            return $isValid;
        }

        return false;
    }

    public function loadFormAttributes()
    {
        $this->order_id = $this->orderDish->order_id;
        $this->dish_id = $this->orderDish->dish_id;
        $this->name = $this->orderDish->name;
        $this->price = $this->orderDish->price;
        $this->quantity = $this->orderDish->quantity;
        $this->portion = $this->orderDish->portion;
    }

    public function setOrderDish(OrderDish $orderDish)
    {
        $this->_orderDish = $orderDish;
    }

    public function getOrderDish()
    {
        if ($this->_orderDish === null) {
            $this->_orderDish = new OrderDish();
        }

        return $this->_orderDish;
    }

    public function attributeLabels()
    {
        return $this->orderDish->attributeLabels();
    }
}