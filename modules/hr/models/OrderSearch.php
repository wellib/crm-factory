<?php

namespace app\modules\hr\models;

use Yii;

use yii\base\Model;

use yii\data\ActiveDataProvider;

use app\modules\hr\models\Employee;

use app\modules\structure\models\Department;
use app\modules\structure\validators\DepartmentValidator;


/**
 * OrderSearch represents the model behind the search form about `app\modules\hr\models\Order`.
 */
class OrderSearch extends Order
{
    public $date_from;
    public $date_to;
    public $structure_department;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'number', 'date', '_employees', 'date_from', 'date_to'], 'safe'],
            [['date_from', 'date_to'], 'default', 'value' => null],
            ['structure_department', DepartmentValidator::className()],
            ['structure_department', 'default', 'value' => null],
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
        $query = Order::find();

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
        $query->andFilterWhere(['like', 'type', $this->type]);
        $query->andFilterWhere(['like', 'number', $this->number]);

        if (!empty($this->_employees)) {
            $employees = Employee::find()->searchByFullName($this->_employees)->all();
            $inList = array_map(function ($employee) {
                /** @var Employee $employee */
                return $employee->getId(false);
            }, $employees);
            $query->andWhere(['in', '_employees', $inList]);
        }

        if (!empty($this->date_from) || !empty($this->date_to)) {
            $query->dateFromTo($this->date_from, $this->date_to);
        }

        if (!empty($this->structure_department)) {
            $sd = Department::find()->id($this->structure_department)->one();
            if ($sd) {
                $employees = Employee::find()->structureDepartment(array_map(function($model){
                    /** @var Department $model */
                    return $model->getId(false);
                }, $sd->getAllChilds(true)))->all();
                $query->andWhere(['in', '_employees', array_map(function($employee) {
                    /** @var Employee $employee */
                    return $employee->getId(false);
                }, $employees)]);
            }
        }


        //$query->andFilterWhere(['like', 'hr_employee.full_name', $this->_employees]);
        //$query->andFilterWhere(['like', '_id', $this->_id])
            //->andFilterWhere(['like', 'created_at', $this->created_at])
            //->andFilterWhere(['like', 'updated_at', $this->updated_at])
            //->andFilterWhere(['like', 'type', $this->type])
            //->andFilterWhere(['like', 'number', $this->number])
            //->andFilterWhere(['like', 'date', $this->date])
            //->andFilterWhere(['like', 'note', $this->note])
            //->andFilterWhere(['like', '_employees', $this->_employees]);

        return $dataProvider;
    }

    public function behaviors()
    {
        return [];
    }
}
