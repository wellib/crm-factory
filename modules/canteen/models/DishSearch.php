<?php

namespace app\modules\canteen\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class DishSearch extends Model
{
    public $name;
    public $week_day;

    public function rules()
    {
        return [
            ['name', 'string'],

            ['week_day', 'integer'],
            ['week_day', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
        ];
    }

    public function search($params)
    {
        $query = Dish::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'week_day' => SORT_ASC,
                ],
            ],
            'pagination' => false,
        ]);

        if ($this->load($params) && !$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['week_day' => $this->week_day]);
        $query->andFilterWhere(['LIKE', 'name', $this->name]);

        return $dataProvider;
    }
}