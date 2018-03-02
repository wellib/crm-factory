<?php

namespace app\modules\hr\models;


use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use app\modules\hr\Module;

use app\modules\structure\models\Department;
use app\modules\structure\validators\DepartmentValidator;

/**
 * Class EmployeeSearch
 * @package app\modules\hr\models
 */
class EmployeeSearch extends Model
{
    public $full_name;
    public $structure_department;
    public $position;
    public $contacts;
    public $employee_id; // это не id записи из базы данных - это "Табельный номер"
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['structure_department', DepartmentValidator::className()],
            ['structure_department', 'default', 'value' => null],
            [['full_name', 'position', 'contacts', 'employee_id'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'full_name'              => Module::t('employee-search', 'ATTR__FULL_NAME__LABEL'),
            'structure_department'   => Module::t('employee-search', 'ATTR__STRUCTURE_DEPARTMENT__LABEL'),
            'position'               => Module::t('employee-search', 'ATTR__POSITION__LABEL'),
            'contacts'               => Module::t('employee-search', 'ATTR__CONTACTS__LABEL'),
            'employee_id'            => Module::t('employee-search', 'ATTR__EMPLOYEE_ID__LABEL'),
        ];
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
        $query = Employee::find();

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

        if (!empty($this->full_name)) {
            $query->searchByFullName($this->full_name);
        }

        $query->andFilterWhere(['like', 'position', $this->position]);
        $query->andFilterWhere(['like', '_company_card.employee_id', $this->employee_id]);
        $query->andFilterWhere(['like', '_contacts.value', $this->contacts]);
        
        if (!empty($this->structure_department)) {
            $sd = Department::find()->id($this->structure_department)->one();
            if ($sd) {
                $query->structureDepartment(array_map(function($model){
                    /** @var Department $model */
                    return $model->getId(false);
                }, $sd->getAllChilds(true)));
            }
        }

        //$query->andFilterWhere(['like', '_id', $this->_id])
        //    ->andFilterWhere(['like', 'created_at', $this->created_at])
        //    ->andFilterWhere(['like', 'updated_at', $this->updated_at])
        //    ->andFilterWhere(['like', 'first_name', $this->first_name])
        //    ->andFilterWhere(['like', 'middle_name', $this->middle_name])
        //    ->andFilterWhere(['like', 'last_name', $this->last_name])
        //    ->andFilterWhere(['like', 'sex', $this->sex])
        //    ->andFilterWhere(['like', 'birthday', $this->birthday])
        //    ->andFilterWhere(['like', '_user', $this->_user])
        //    ->andFilterWhere(['like', '_identity_card', $this->_identity_card])
        //    ->andFilterWhere(['like', '_enterprise', $this->_enterprise])
        //    ->andFilterWhere(['like', '_contacts', $this->_contacts])
        //    ->andFilterWhere(['like', '_education', $this->_education])
        //    ->andFilterWhere(['like', '_family', $this->_family])
        //    ->andFilterWhere(['like', '_experience', $this->_experience])
        //    ->andFilterWhere(['like', '_files', $this->_files]);

        return $dataProvider;
    }
}
