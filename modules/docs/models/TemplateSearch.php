<?php

namespace app\modules\docs\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\docs\models\Template;
use app\modules\accounts\models\User;
/**
 * TemplateSearch represents the model behind the search form about `app\modules\docs\models\Template`.
 */
class TemplateSearch extends Template
{
    public $date_from;
    public $date_to;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
							'_id',
							'id',
							'name',
							'date_from',
							'date_to',
							'status',
							],
						'safe'],
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
        $query = Template::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
						'sort'=> ['defaultOrder' => ['status'=>SORT_ASC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if (!empty($this->company)) {
            $query->andWhere(['company' => $this->company]);
        }
        if (!empty($this->date_from)) {
            $query->andWhere(['>=', 'date_timestamp', strtotime($this->date_from . ' 00:00:00')]);
        }
        if (!empty($this->date_to)) {
            $query->andWhere(['<=', 'date_timestamp', strtotime($this->date_to . ' 23:59:59')]);
        }
        if (!empty($this->id)) {
            $query->andWhere(['id' => intval($this->id)]);
        }

        if (!empty($this->name)) {
						$query->andFilterWhere(['like', 'name', $this->name]);
				}
				

				$userID = Yii::$app->getUser()->getId();
				$user = User::find()->where(['_id' => $userID])->one();

				if (!$user->getStatus()) {
					$qusers = Template::find();
					$qusers->andWhere(['=', '_author', $userID]);
					$query->orWhere($qusers->where);

					$subQuery = Template::find();
					$subQuery->where([
							'status' => 1, 
					]);
					$query->orWhere($subQuery->where);
				}

        return $dataProvider;
    }
}
