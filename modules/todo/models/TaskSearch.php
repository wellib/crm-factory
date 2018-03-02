<?php

namespace app\modules\todo\models;

use app\modules\accounts\models\User;
use app\modules\todo\Module;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\todo\models\Task;
use yii\helpers\VarDumper;

/**
 * TaskSearch represents the model behind the search form about `app\modules\todo\models\Task`.
 */
class TaskSearch extends Model
{
    public $id;
    public $subject;
    public $deadline_timestamp;
    public $priority;
    public $status;

    public $users;
    public $deadline_timestamp_from;
    public $deadline_timestamp_to;
    public $_author;
    public $_users_approve_execute;
    public $_users_check_result;
    public $_users_performers;

    /**
     * @var User
     */
    protected $_userModel;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'id',
                'subject',
                'deadline_timestamp',
                'deadline_timestamp_from',
                'deadline_timestamp_to',
                'priority',
                'users',
                'status',
                '_author',
                '_users_approve_execute',
                '_users_check_result',
                '_users_performers',
            ],'safe'],
            [['priority', 'id'], 'filter', 'filter' => function($value) {
                return !empty($value) ? intval($value) : null;
            }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return [
            self::SCENARIO_DEFAULT => [
                'id',
                'subject',
                'deadline_timestamp',
                'deadline_timestamp_from',
                'deadline_timestamp_to',
                'users',
                'priority',
                'status',
                '_author',
                '_users_approve_execute',
                '_users_check_result',
                '_users_performers',
            ],
        ];
    }
    
    //public function attributeLabels()
    //{
    //    return array_merge(parent::attributeLabels(),[
    //        'users' => Module::t('task', 'TASK_SEARCH__ATTR__USERS__LABEL'),
    //    ]);
    //}

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Task::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->setScenario(self::SCENARIO_DEFAULT);
        $this->load($params);
        //VarDumper::dump($params, 10, true);
        //VarDumper::dump($this->priority, 10, true);

        if (!$this->validate()) {
            //var_dump($this->getErrors());
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        //var_dump(1);
        // grid filtering conditions
        $subQuery = Task::find();
        $subQuery
            ->andFilterWhere(['like', 'subject', $this->subject])
            ->andFilterWhere(['like', 'status', $this->status]);
            //->andFilterWhere(['like', 'deadline_timestamp', !empty($this->deadline_timestamp) ? strtotime($this->deadline_timestamp) : null])

        if(!empty($this->deadline_timestamp)) {
            $subQuery->andWhere(['between', 'deadline_timestamp', strtotime($this->deadline_timestamp . ' 00:00:00'), strtotime($this->deadline_timestamp . ' 23:59:59')]);
        }

        if (!empty($this->deadline_timestamp_from)) {
            $subQuery->andWhere(['>=', 'deadline_timestamp', strtotime($this->deadline_timestamp_from . ' 00:00:00')]);
        }
        if (!empty($this->deadline_timestamp_to)) {
            $subQuery->andWhere(['<=', 'deadline_timestamp', strtotime($this->deadline_timestamp_to . ' 23:59:59')]);
        }

        //var_dump($this->priority);
        //var_dump($this->priority);
        if (!empty($this->priority)) {
            $subQuery->andWhere(['priority' => $this->priority]);
        }
        if (!empty($this->id)) {
            $subQuery->andWhere(['id' => $this->id]);
        }

        if (!empty($this->users)) {
            $usersSubQuery = Task::find();
            $usersSubQuery->orWhere(['_users_performers'      => $this->users]);
            $usersSubQuery->orWhere(['_users_control_results' => $this->users]);
            $usersSubQuery->orWhere(['_users_approve_execute' => $this->users]);
            $usersSubQuery->orWhere(['_author' => $this->users]);
            $this->_userModel = User::find()->where(['_id' => $this->users])->one();
            $subQuery->andWhere($usersSubQuery->where);
        }

        if (!empty($this->_author)) {
            $subQuery->andWhere(['_author' => $this->_author]);
        }
        if (!empty($this->_users_approve_execute)) {
            $subQuery->andWhere(['_users_approve_execute' => $this->_users_approve_execute]);
        }
        if (!empty($this->_users_performers)) {
            $subQuery->andWhere(['_users_performers' => $this->_users_performers]);
        }

        if (!empty($this->_users_control_results)) {
            $subQuery->andWhere(['_users_control_results' => $this->_users_control_results]);
        }



        //->andFilterWhere(['like', 'description', $this->description])
            //->andFilterWhere(['like', '_attached_files', $this->_attached_files])
            //->andFilterWhere(['like', '_author', $this->_author])
            //->andFilterWhere(['like', '_users_performers', $this->_users_performers])
            //->andFilterWhere(['like', '_users_control_execution', $this->_users_control_execution])
            //->andFilterWhere(['like', '_users_control_results', $this->_users_control_results])
            //->andFilterWhere(['like', '_users_notify_after_finished', $this->_users_notify_after_finished])
            //->andFilterWhere(['like', '_users_approve_execute', $this->_users_approve_execute])
            //->andFilterWhere(['like', 'created_at', $this->created_at])
            //->andFilterWhere(['like', 'updated_at', $this->updated_at]);
        $query->andWhere($subQuery->where);
        return $dataProvider;
    }

    public function behaviors()
    {
        return [];
    }

    /**
     * @return User
     */
    public function getUserModel()
    {
        return $this->_userModel;
    }
}
