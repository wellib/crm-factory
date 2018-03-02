<?php

namespace app\modules\hr\models\embedded;

use yii\base\Model;

/**
 * Class EmbeddedModel
 * @package app\modules\hr\models\embedded
 */
class EmbeddedModel extends Model
{
    /**
     * @param Model $model
     * @param string $attribute
     * @param array $data
     * @param bool $runValidation
     * @param bool $leastOneModel
     * @return bool
     */
    public static function loadMultipleModelsData($model, $attribute, $data, $runValidation = false, $leastOneModel = false)
    {
        $formName = (new static())->formName();

        /**
         * из за возможности удаления данных, индексы могут иди с пробелами (прим: 0, 3, 9)
         * Соответственно и в базу эти индексы тоже записывается как 0, 3, 9
         * Для того что бы сбросить индексы до 0, 1, 2 - нужена строчка ниже
         */
        $data[$formName] = isset($data[$formName]) ? array_values($data[$formName]) : [];

        $model->$attribute = array_map(function(){
            return new static();
        }, isset($data[$formName]) && is_array($data[$formName]) ? $data[$formName] : []);

        if ($leastOneModel === true && count($model->$attribute) === 0) {
            $model->$attribute = [
                new static(),
            ];
        }

        $valid = true;
        if (static::loadMultiple($model->$attribute, $data) || $leastOneModel === true) { // сделаем массовую загрузку данных в путые модели которые мы снасоздавали ранее
            if ($runValidation === true) {
                $valid = static::validateMultiple($model->$attribute); // сделаем массовую валидацию всех моделей
            }
        }

        return $valid;
    }
}