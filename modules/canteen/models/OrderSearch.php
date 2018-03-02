<?php

namespace app\modules\canteen\models;

use app\validators\MongoObjectIdValidator;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class OrderSearch extends Model
{
    public $created_at_range;
    public $created_at_from;
    public $created_at_from_ts;
    public $created_at_to;
    public $created_at_to_ts;
    public $employee_ids;

    public function rules()
    {
        return [
            ['created_at_from', 'date'],
            ['created_at_from', function () {
                $this->created_at_from_ts = (int)Yii::$app->formatter->asTimestamp($this->created_at_from);
            }],

            ['created_at_to', 'date'],
            ['created_at_to', function () {
                $this->created_at_to_ts = (int)Yii::$app->formatter->asTimestamp($this->created_at_to) + 1 * 24 * 60 * 60;
            }],

            ['employee_ids', 'each', 'rule' => [MongoObjectIdValidator::className()]],
        ];
    }

    public function search($params)
    {
        $query = Order::find();
        $query->with('orderDishList', 'employee');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'defaultPageSize' => 50,
            ],
        ]);

        if ($this->load($params) && !$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['>=', 'created_at', $this->created_at_from_ts]);
        $query->andFilterWhere(['<', 'created_at', $this->created_at_to_ts]);
        $query->andFilterWhere(['employee_id' => $this->employee_ids]);

        return $dataProvider;
    }

    public function attributeLabels()
    {
        return [
            'created_at_range' => 'Период',
            'employee_ids' => 'Сотрудники',
        ];
    }
}