<?php
namespace app\modules\canteen\models;

use Yii;
use yii\base\Model;
use yii\bootstrap\Html;

/**
 * @property Dish $dish
 */
class DishForm extends Model
{
    public $name;
    public $week_day;
    public $price;
    public $portion;

    /**
     * @var Dish
     */
    private $_dish;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = $this->dish->rules();

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->loadFormAttributes();
    }

    public function loadModelAttributes()
    {
        if ($this->validate()) {

            $this->dish->name = $this->name;
            $this->dish->week_day = $this->week_day;
            $this->dish->price = $this->price;
            $this->dish->portion = $this->portion;

            $valid = $this->dish->validate();
            if (!$valid) {
                Yii::$app->session->addFlash('error', Html::errorSummary($this->dish));
            }
            return $valid;
        }

        return false;
    }

    public function loadFormAttributes()
    {
        $this->name = $this->dish->name;
        $this->week_day = $this->dish->week_day;
        $this->price = $this->dish->price;
        $this->portion = $this->dish->portion;
    }

    public function setDish(Dish $dish)
    {
        $this->_dish = $dish;
    }

    public function getDish()
    {
        if ($this->_dish === null) {
            $this->_dish = new Dish();
        }

        return $this->_dish;
    }

    public function attributeLabels()
    {
        return $this->dish->attributeLabels();
    }
}