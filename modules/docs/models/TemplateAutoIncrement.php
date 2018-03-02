<?php
/**
 * Created by PhpStorm.
 * User: stasm
 * Date: 18.07.2016
 * Time: 9:17
 */

namespace app\modules\docs\models;

/**
 * Класс реализующий счетчик инкрементация числового уникального id для модели Contract
 * Т.е. эмуляция auto_increment как в MySQL
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property mixed $key
 * @property integer $last_auto_increment_id Последний id добавленный в базу
 */
class TemplateAutoIncrement extends Settings
{
    const KEY = 'template-auto-increment';

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return array_merge(parent::attributes(),[
            'last_auto_increment_id',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['last_auto_increment_id'], 'integer'],
            [['last_auto_increment_id'], 'default', 'value' => 0],
        ]);
    }

    /**
     * Загружает модель или создает новую если ранее не была создана
     *
     * @return ContractAutoIncrement|array|null
     */
    protected static function getModel()
    {
        if (($model = self::find()->key(self::KEY)->one()) === null) {
            $model = new self();
            $model->key = self::KEY;
            $model->validate();
        }
        return $model;
    }

    /**
     * Возращает инкрементированный(следующи +1) id
     * @return int
     */
    public static function getNextAutoIncrementID()
    {
        $model = self::getModel();
        $model->last_auto_increment_id++;
        $model->save(false);
        return $model->last_auto_increment_id;
    }
}
