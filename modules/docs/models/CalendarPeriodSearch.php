<?php

namespace app\modules\docs\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\docs\models\CalendarPeriod;

/**
 * CalendarPeriodSearch represents the model behind the search form about `app\modules\docs\models\CalendarPeriod`.
 */
class CalendarPeriodSearch extends CalendarPeriod
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id', 'from_date', 'to_date', 'every_year', 'name', 'type', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = CalendarPeriod::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere(['like', '_id', $this->_id])
            //->andFilterWhere(['like', 'from_date', $this->from_date])
            //->andFilterWhere(['like', 'to_date', $this->to_date])
            ->andFilterWhere(['like', 'every_year', $this->every_year])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'type', $this->type]);
            //->andFilterWhere(['like', 'created_at', $this->created_at])
            //->andFilterWhere(['like', 'updated_at', $this->updated_at]);

        if (($fromDate = strtotime($this->from_date)) !== false) {
            $query->andWhere(['>=', 'from_date', $fromDate]);
        }

        if (($toDate = strtotime($this->to_date)) !== false) {
            //var_dump($toDate);
            $query->andWhere(['<=', 'to_date', $toDate + (60 * 60 * 23)]);
        }

        return $dataProvider;
    }
}
