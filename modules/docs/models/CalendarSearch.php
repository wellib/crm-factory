<?php

namespace app\modules\docs\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\docs\models\Calendar;

/**
 * CalendarSearch represents the model behind the search form about `app\modules\docs\models\Calendar`.
 */
class CalendarSearch extends Calendar
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id', 'timestamp', 'type', 'name', 'date', 'date_to', 'description', 'status', '_author', 'created_at', 'updated_at'], 'safe'],
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
        $query = Calendar::find();

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

        $query->andFilterWhere(['=', '_author', Yii::$app->getUser()->getId()]);

				if (!isset($this->type) && !isset($this->name)) {
					if (isset($_SESSION['type']))
						$this->type = $_SESSION['type'];
					else {
						$this->type = 'work';
						$_SESSION['type'] = $this->type;
					}
}
		
        // grid filtering conditions
        $query->andFilterWhere(['like', '_id', $this->_id])
            ->andFilterWhere(['like', 'timestamp', $this->timestamp])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);

        return $dataProvider;
    }
}
