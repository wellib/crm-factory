<?php

namespace app\modules\docs\models;

use Yii;

/**
 * This is the ActiveQuery class for [[Task]].
 *
 * @see Task
 */
class TaskQuery extends \yii\mongodb\ActiveQuery
{
    /**
     * @inheritdoc
     * @return Task[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Task|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * Выборка шаблонных(повторящихся задача)
     */
    public function template()
    {
        return $this->andWhere(['template' => true]);
    }

    /**
     * Выборка по автору
     *
     * @param $_id
     * @return $this
     */
    public function author($_id)
    {
        return $this->andWhere(['_author' => $_id]);
    }


    /**
     * Выборка по статусу
     *
     * @param mixed $status
     * @return $this
     */
    public function status($status)
    {
        if (is_array($status)) {
            return $this->andWhere(['in', 'status', $status]);
        }
        return $this->andWhere(['status' => $status]);
    }





    /**
     * Задачи в которых я принимаю участие в определенной роли(согласующий, исполнитель, контролирующий результат), которые НЕ ПРОСРОЧЕННЫЕ и ждут от меня действия(ответа)
     *  - от согласующего    ждут решения по согласованию
     *  - от исполнителя     ждут уведомления о завершении работы
     *  - от контролирующего ждет проверки результата
     *
     * @return TaskQuery
     */
    public function inboxAwaitingResponse()
    {
        $userID = Yii::$app->getUser()->getId(); // id пользователя (мой id)
        $now = Task::getNow(); // timestamp "сейчас"

        $subQuery = Task::find();

        /**
         * Задачи в которых я определен как исполнитель и которые сейчас имеют статус  "в процессе выполнения"
         */
        $performers = Task::find();
        $performers->where([
            '_users_performers' => $userID, // пользователь есть в спике исполнителей
            'status' => Task::STATUS__IN_PROGRESS, // статус задачи "выполняется"
        ]);
        $performers->andWhere(['!=','_users_performers_finished.'.$userID, true]); // я еще не завершил выполнение данной задачи
        //$performers->andWhere(['>=', 'deadline_timestamp', $now]); // срок выполнения задачи БОЛЬШЕ чем "сейчас"
        $performers->andWhere(['<=', 'perform_timestamp', $now]); // срок начала выполнения задачи МЕНЬШЕ чем "сейчас" 
        $subQuery->orWhere($performers->where);

        //$this->andWhere($subQuery->where);
        //return $this;

        /**
         * Задачи в которых я выполняю роль того кто должен дать свое согласие на выполнение
         */
        $approve = Task::find();
        $approve->where([
            '_users_approve_execute' => $userID, // пользователь есть в списке согласующих
            '_users_approve_execute_response.' . $userID => null, // пользователь еще не оставил свой ответ
            'status' => Task::STATUS__APPROVAL_AWAITING, // статус задачи "на согласовании"
        ]);
        $approve->andWhere(['>=', 'approve_execute_deadline_timestamp', $now]); // установленный срок на согласование задачи БОЛЬШЕ чем "сейчас"
		$approve->andWhere(['<>', 'template', true]);   
		$subQuery->orWhere($approve->where);



        /**
         * Задачи в которых я выполняю роль того кто должен проверить результат выполненной работы
         */
        $check = Task::find();
        $check->where([
            '_users_check_result' => $userID, // пользователь есть в списке тех кто должне проверить результат
            '_users_check_result_response.' . $userID => null, // пользователь еще не оставил свой ответ
            'status' => Task::STATUS__CHECK_RESULTS_AWAITING, // статус задачи "на проверке результата"
        ]);
        $check->andWhere(['>=', 'check_results_deadline_timestamp', $now]); // установленный срок на проверку результата выполенной задачи БОЛЬШЕ чем "сейчас"
        $subQuery->orWhere($check->where);

        $this->andWhere($subQuery->where);
        return $this;
    }

    /**
     * Задачи в которых я принимаю участие в определенной роли(согласующий, исполнитель, контролирующий результат), которые уже ПРОСРОЧЕННЫЕ и ждут от меня действия(ответа)
     *  - от согласующего    ждут решения по согласованию
     *  - от исполнителя     ждут уведомления о завершении работы
     *  - от контролирующего ждет проверки результата
     *
     * @return TaskQuery
     */
    public function inboxOverdue()
    {
        $userID = Yii::$app->getUser()->getId(); // id пользователя (мой id)
        $now = Task::getNow(); // timestamp "сейчас"

        $subQuery = Task::find();

        /**
         * Задачи в которых я определен как исполнитель и которые сейчас имеют статус  "в процессе выполнения"
         */
        $performers = Task::find();
        $performers->where([
            '_users_performers' => $userID, // пользователь есть в вспике исполнителей
            'status' => Task::STATUS__IN_PROGRESS, // статус задачи "выполняется"
        ]);
        //var_dump($now);
        //var_dump(date('d.m.Y H:i:s', $now));
        //var_dump(date('d.m.Y H:i:s', 1472040900));
        //var_dump($now > 1472040900);
        //1440439200
        //1472040900
        //var_dump($now < 1472040900);
        $performers->andWhere(['!=','_users_performers_finished.'.$userID, true]); // я еще не завершил выполнение данной задачи
        $performers->andWhere(['<', 'deadline_timestamp', $now]); // срок выполнения задачи МЕНЬШЕ чем "сейчас" (т.е. просрочено)
        $subQuery->orWhere($performers->where);

        /**
         * Задачи в которых я выполняю роль того кто должен дать свое согласие на выполнение
         */
        //var_dump($userID);
        $approve = Task::find();
        $approve->where([
            '_users_approve_execute' => $userID, // пользователь есть в списке согласующих
            '_users_approve_execute_response.' . $userID => null, // пользователь еще не оставил свой ответ
            'status' => Task::STATUS__APPROVAL_AWAITING, // статус задачи "на согласовании"
        ]);
        $approve->andWhere(['<', 'approve_execute_deadline_timestamp', $now]); // установленный срок на согласование МЕНЬШЕ чем "сейчас" (т.е. просрочено)
        $subQuery->orWhere($approve->where);

        /**
         * Задачи в которых я выполняю роль того кто должен проверить результат выполненной работы
         */
        $check = Task::find();
        $check->where([
            '_users_check_result' => $userID, // пользователь есть в списке контролирующих
            '_users_check_result_response.' . $userID => null, // пользователь еще не оставил свой ответ
            'status' => Task::STATUS__CHECK_RESULTS_AWAITING, // статус задачи "на проверке результата"
        ]);
        $check->andWhere(['<', 'check_results_deadline_timestamp', $now]); // установленный срок на проверку результата выполенной задачи МЕНЬШЕ чем "сейчас" (т.е. просрочено)
        $subQuery->orWhere($check->where);

        $this->andWhere($subQuery->where);
        return $this;
    }

    /**
     * Задачи в которых я принимаю участие в роли исполнителя и которые сейчас находятся на проверке
     *
     * @return TaskQuery
     */
    public function inboxCheck()
    {
        $userID = Yii::$app->getUser()->getId();

        $subQuery = Task::find();

        $subQuery->andWhere([
            '_users_performers' => $userID, // пользователь есть в списке согласующих
            'status' => Task::STATUS__CHECK_RESULTS_AWAITING, // статус "на проверке результата"
        ]);

        $this->andWhere($subQuery->where);

        return $this;
    }

    /**
     * Задачи в которых я принимаю(принимал) участие в определенной роли(согласующий, исполнитель, контролирующий результат) и который выполнены
     *
     * @return TaskQuery
     */
    public function inboxDone()
    {
        $userID = Yii::$app->getUser()->getId();

        $subQuery = Task::find();

        $subQuery->orWhere(['_users_approve_execute'       => $userID]);
        $subQuery->orWhere(['_users_performers'            => $userID]);
        $subQuery->orWhere(['_users_check_result'          => $userID]);
        $subQuery->orWhere(['_users_notify_after_finished' => $userID]);

        $subQuery->status(Task::STATUS__DONE);

        $this->andWhere($subQuery->where);

        return $this;
    }








    /**
     * Я автор задачи и она находится на согласовании(или не прошла согласование) и СРОКИ СОГЛАСОВАНИЯ НЕ ПРОСРОЧЕНЫ
     *
     * @return TaskQuery
     */
    public function outboxApproving()
    {
        $userID = Yii::$app->getUser()->getId();
        $now = Task::getNow(); // timestamp "сейчас"

        $subQuery = Task::find();
        $subQuery->orWhere(['status' => Task::STATUS__APPROVAL_AWAITING ]); // стасту "на согласовании"
        $subQuery->orWhere(['status' => Task::STATUS__APPROVAL_FAILED   ]); // стасту "не прошла согласование"

        $subQuery->andWhere(['>=', 'approve_execute_deadline_timestamp', $now]); // срок на согласование больче чем "сейчас" (т.е. НЕ ПРОСРОЧЕНА)
        $subQuery->author($userID);

        $this->andWhere($subQuery->where);

        return $this;
    }

    /**
     * Я автор задачи и она выполняется и СРОКИ ВЫПОЛНЕНИЯ НЕ ПРОСРОЧЕНЫ
     *
     * @return TaskQuery
     */
    public function outboxPerformed()
    {
        $userID = Yii::$app->getUser()->getId();
        $now = Task::getNow(); // timestamp "сейчас"

        $subQuery = Task::find();
        $subQuery->andWhere(['status' => Task::STATUS__IN_PROGRESS]); // статус "выполняется"
        $subQuery->andWhere(['>=', 'deadline_timestamp', $now]); // срок выполнения больше чем "сейчас" (т.е. НЕ ПРОСРОЧЕНА)

        $subQuery->author($userID);

        $this->andWhere($subQuery->where);

        return $this;
    }

    /**
     * Я автор задачи и она ПРОСРОЧЕНА по определенному статус
     *  - просрочен срок согласования
     *  - просрочен срок выполнения
     *  - просрочен срок проверки результата
     *
     * @return TaskQuery
     */
    public function outboxExpired()
    {
        $userID = Yii::$app->getUser()->getId();
        $now = Task::getNow(); // timestamp "сейчас"

        $subQuery = Task::find();

        $performers = Task::find();
        $performers->where([
            'status' => Task::STATUS__IN_PROGRESS,
        ]);
        $performers->andWhere(['<', 'deadline_timestamp', $now]);
        $subQuery->orWhere($performers->where);


        $approve = Task::find();
        $approve->andWhere(['in' , 'status', [
            Task::STATUS__APPROVAL_AWAITING,
            Task::STATUS__APPROVAL_FAILED,
        ]]);
        $approve->andWhere(['<', 'approve_execute_deadline_timestamp', $now]);
        $subQuery->orWhere($approve->where);


        $check = Task::find();
        $check->andWhere(['in' , 'status', [
            Task::STATUS__CHECK_RESULTS_AWAITING,
            Task::STATUS__CHECK_RESULTS_FAILED,
        ]]);
        $check->andWhere(['<', 'check_results_deadline_timestamp', $now]);
        $subQuery->orWhere($check->where);

        $subQuery->author($userID);

        $this->andWhere($subQuery->where);

        return $this;
    }

    /**
     * Я автор задачи и она выполнена
     *
     * @return TaskQuery
     */
    public function outboxDone()
    {
        $userID = Yii::$app->getUser()->getId();

        $subQuery = Task::find();

        $subQuery->status(Task::STATUS__DONE);
        $subQuery->author($userID);

        $this->andWhere($subQuery->where);

        return $this;
    }

    /**
     * Я автор задачи и она находится на проверке при этом срок проверки не просрочен
     *
     * @return TaskQuery
     */
    public function outboxCheck()
    {
        $userID = Yii::$app->getUser()->getId();
        $now = Task::getNow(); // timestamp "сейчас"

        $subQuery = Task::find();

        $subQuery->where(['status' => Task::STATUS__CHECK_RESULTS_AWAITING ]);

        $subQuery->andWhere(['>=', 'check_results_deadline_timestamp', $now]);
        $subQuery->author($userID);

        $this->andWhere($subQuery->where);

        return $this;
    }

}
