<?php
namespace app\modules\canteen\models;

use app\validators\MongoObjectIdValidator;
use yii\mongodb\ActiveRecord;

/**
 * @property string $_id
 * @property string $order_id
 * @property integer $dish_id
 * @property string $name
 * @property double $price
 * @property integer $quantity
 * @property double $portion
 */
class OrderDish extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'canteen_order_dish';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['dish_id', 'required'],
            ['dish_id', MongoObjectIdValidator::className()],

            ['order_id', 'required'],
            ['order_id', MongoObjectIdValidator::className()],

            ['quantity', 'required'],
            ['quantity', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'order_id',
            'dish_id',
            'name',
            'price',
            'quantity',
            'portion',
        ];
    }
}