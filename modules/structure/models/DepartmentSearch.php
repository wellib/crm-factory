<?php

namespace app\modules\structure\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\structure\models\Department;

/**
 * DepartmentSearch represents the model behind the search form about `app\modules\structure\models\Department`.
 */
class DepartmentSearch extends Department
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id', 'name', 'icon', '_parent', 'created_at', 'updated_at'], 'safe'],
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
        $query = Department::find();

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
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'icon', $this->icon])
            ->andFilterWhere(['like', '_parent', $this->_parent])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);

        return $dataProvider;
    }
}
