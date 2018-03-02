<?php

namespace app\modules\docs\models;


/**
 * This is the model class for collection "docs_settings".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property mixed $key
 */
class Settings extends \yii2tech\embedded\mongodb\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'docs_settings';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'key',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'key' => 'Key',
        ];
    }

    /**
     * @return SettingsQuery
     */
    public static function find()
    {
        return new SettingsQuery(get_called_class());
    }
}
