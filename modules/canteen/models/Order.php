<?php
namespace app\modules\canteen\models;

use app\behaviors\MongoBlameableBehavior;
use app\modules\accounts\models\User;
use app\validators\MongoObjectIdValidator;
use yii\behaviors\TimestampBehavior;
use yii\mongodb\ActiveRecord;

/**
 * @property string $_id
 * @property string $employee_id
 *
 * @property User $employee
 * @property OrderDish[] $orderDishList
 */
class Order extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'canteen_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['employee_id', 'required'],
            ['employee_id', MongoObjectIdValidator::className()],

            ['created_by', MongoObjectIdValidator::className()],

            ['updated_by', MongoObjectIdValidator::className()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            MongoBlameableBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'employee_id',
        ];
    }

    public function getEmployee()
    {
        return $this->hasOne(User::className(), ['_id' => 'employee_id']);
    }

    public function getOrderDishList()
    {
        return $this->hasMany(OrderDish::className(), ['order_id' => '_id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'Ид',
            'employee_id' => 'Сотрудник',
        ];
    }
}