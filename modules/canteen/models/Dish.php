<?php
namespace app\modules\canteen\models;

use app\modules\canteen\components\DaysWeek;
use yii\behaviors\AttributeTypecastBehavior;
use yii\mongodb\ActiveRecord;

/**
 * @property string $_id
 * @property string $name
 * @property integer $week_day
 * @property double $price
 * @property double $portion
 */
class Dish extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'canteen_dish';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'trim'],
            ['name', 'default'],
            ['name', 'required'],
            ['name', 'string'],

            ['week_day', 'required'],
            ['week_day', 'integer'],
            ['week_day', 'in', 'range' => DaysWeek::getDayNumbers()],

            ['price', 'required'],
            ['price', 'double', 'numberPattern' => '/^[0-9]*\.?[0-9]{0,2}$/'],

            ['portion', 'required'],
            ['portion', 'double', 'numberPattern' => '/^[0-9]*\.?[0-9]{0,2}$/'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'typecast' => [
                'class' => AttributeTypecastBehavior::className(),
                'attributeTypes' => [
                    'name' => AttributeTypecastBehavior::TYPE_STRING,
                    'week_day' => AttributeTypecastBehavior::TYPE_INTEGER,
                    'price' => AttributeTypecastBehavior::TYPE_FLOAT,
                    'portion' => AttributeTypecastBehavior::TYPE_FLOAT,
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'id',
            'name',
            'week_day',
            'price',
            'portion',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Название',
            'week_day' => 'День недели',
            'price' => 'Цена',
            'portion' => 'Порция',
        ];
    }
}