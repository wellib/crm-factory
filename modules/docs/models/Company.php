<?php

namespace app\modules\docs\models;

use Yii;

/**
 * This is the model class for collection "docs_company".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property mixed $name
 */
class Company extends \yii\mongodb\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'docs_company';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'name',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'name' => 'Название',
        ];
    }
}
