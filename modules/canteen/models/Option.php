<?php
namespace app\modules\canteen\models;

use yii\behaviors\AttributeTypecastBehavior;
use yii\mongodb\ActiveRecord;

/**
 * @property string $_id
 * @property string $name
 * @property string $code
 * @property string $value
 */
class Option extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'canteen_option';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'string'],

            ['code', 'string'],

            ['value', 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'name',
            'code',
            'value',
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
                    'code' => AttributeTypecastBehavior::TYPE_STRING,
                    'value' => AttributeTypecastBehavior::TYPE_STRING,
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Название',
            'code' => 'Код',
            'value' => 'Значение',
        ];
    }
}