<?php

namespace app\modules\docs\controllers;

use app\modules\accounts\models\User;
use app\modules\docs\models\TaskComment;
use app\modules\docs\models\TaskLog;
use app\modules\docs\models\TaskQuery;
use Yii;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\modules\docs\models\TaskSearch;
use app\modules\docs\models\Task;
use yii\web\UploadedFile;


/**
 * TaskController implements the CRUD actions for Task model.
 */
class TaskController extends Controller
{
    const FIND_USERS__RESULTS_PER_PAGE = 10;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                //'only' => ['create', 'update'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    protected static function iconv($str)
    {
        //return $str;
        return mb_convert_encoding($str, 'windows-1251', 'UTF-8');
        //return iconv('cp1251', Yii::$app->charset, $str);
    }

    /**
     * Lists all Task models.
     * @return mixed
     */
    public function actionIndex($export = null)
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Task models.
     * @return mixed
     */
    public function actionIndex2($export = null)
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $userID = Yii::$app->getUser()->getId();

        $subQuery = Task::find();

        $subQuery->orWhere(['_users_approve_execute'       => $userID]);
        $subQuery->orWhere(['_users_performers'            => $userID]);
        $subQuery->orWhere(['_users_check_result'          => $userID]);
        $subQuery->orWhere(['_users_notify_after_finished' => $userID]);
        $subQuery->orWhere(['_author' => $userID]);


        $dataProvider->query->andWhere($subQuery->where);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Task model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->getSession()->hasFlash(TaskLog::NOTIFY_SET_STATUS_DONE)) {
            return $this->render('done', [
                'model' => $model,
            ]);
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }
    
    public function actionDownloadAttachedFile($id, $filename, $type = null)
    {
        if ($type == 'log') {
            $comment = TaskLog::find()->where(['_id' => $id])->one(); /** @var TaskLog $comment */
            if (!$comment) {
                throw new ForbiddenHttpException('Комментарий не найден!');
            }
            if (!$comment->task->checkAvailableAccess(Yii::$app->getUser()->getId())) {
                throw new ForbiddenHttpException('У вас нет доступа к данному файлу');
            }
            $allFiles = $comment->getAttachedFilesPaths();
        } else {
            $model = $this->findModel($id);
            if (!$model->checkAvailableAccess(Yii::$app->getUser()->getId())) {
                throw new ForbiddenHttpException('У вас нет доступа к данному файлу');
            }
            $allFiles = $model->getAttachedFilesPaths();
        }

        



        if (!isset($allFiles[$filename]) || !file_exists($allFiles[$filename]['file_path'])) {
            throw new NotFoundHttpException('Файл не найден, возможно он был удален');
        }
        //$filename = $allFiles[$filename];
        header("Content-Length: " . filesize($allFiles[$filename]['file_path']));
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $allFiles[$filename]['filename_orig']);
        readfile($allFiles[$filename]['file_path']);
    }

    public function actionView2($id)
    {
        return $this->render('view2', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionNextComments($id, $total = 0)
    {
        $task = $this->findModel($id);
        $currentTotal = TaskLog::find()->where(['task_id' => $task->_id])->count();
        if ($total < $currentTotal) {
            foreach (TaskLog::find()->where(['task_id' => $task->_id])->orderBy(['_id' => SORT_ASC])->offset($total)->all() as $model) {
                echo $this->renderPartial('log/'.$model->type, ['model' => $model]);
            }
        }
        Yii::$app->end();
    }

    public function actionSendComment($id)
    {
        $task = $this->findModel($id);
        $model = new TaskLog();
        //$model->setScenario(TaskLog::SCENARIO_COMMENT);
        if ($model->load(Yii::$app->request->post()) ) {
            $model->task_id = $task->_id;
            $model->_user = Yii::$app->getUser()->getIdentity()->_id;
            $model->type = TaskLog::COMMENT;
            $model->attachedFilesUpload = UploadedFile::getInstances($model, 'attachedFilesUpload');
            $model->uploadAttachedFiles();
            if (!empty($model->comment)) {
                $model->save(false);
            }
            //var_dump($model->save(false));
            //$model->save(false);
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderPartial('log/_comment-form', [
                'model' => new TaskLog(),
                'task_id' => $id,
            ]);
        } else {
            return $this->redirect(['view2', 'id' => $id]);
        }
    }

    public function actionView3($id)
    {
        $task = $this->findModel($id);

        $comment = new TaskComment();
        if ($comment->load(Yii::$app->request->post()) && $comment->validate()) {
            $comment->_task = $task->_id;
            $comment->_user = Yii::$app->getUser()->getId();
            $comment->save(false);
            $comment = new TaskComment();
        }

        $comments = TaskComment::find()->where([
            '_task' => $task->_id,
        ])->orderBy(['_id' => SORT_DESC])->all();

        return $this->render('view3', [
            'model' => $task,
            'comments' => $comments,
            'comment' => $comment,
        ]);
    }

    /**
     * Creates a new Task model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->formAction(null);
    }

    /**
     * Updates an existing Task model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        return $this->formAction($id);
    }

    protected function formAction($id = null)
    {
        $request = Yii::$app->request;

        if ($id !== null) {
            $model = $this->findModel($id);
            $scenario = $model->deadline_type;
            $model->setScenario($scenario);
        } else {
            $model = new Task();
            $scenario = Task::DEADLINE_TYPE__ONE_TIME;
            $post = $request->post($model->formName());
            if (isset($post['deadline_type'])) {
                $scenario = $post['deadline_type'];
            }
            $model->setScenario($scenario);
        }

        $isNewRecord = $model->isNewRecord;

        $oldModelData = $model->toArray($model->listenChangeAttributes());



        if ($isNewRecord === true) {
            //$model->deadline_time = $model->getTimeEndValue();
        }

        if ($model->load(Yii::$app->request->post())){


            if ($model->validate()) {
                $userID = Yii::$app->getUser()->getId();
                if ($model->isNewRecord) {
                    
                    if (count($model->_users_performers) > 0 && !in_array($userID, $model->_users_performers)) {
                        /** Если в задаче указаны исполнители и меня нет в списке исполнителей */

                        $check = $model->_users_check_result;
                        if (!in_array($userID, $check)) {
                            /** Добавлю себя в списке тех кто должен будет проверить результат выполненной задачи */
                            $check[] = $userID;
                        }
                        $model->_users_check_result = $check;
                    } else {
                        /** Если не указаны исполнители, то добавлю себя в список исполнителей (т.е. моя личная задача) */
                        $model->_users_performers = [$userID];
                    }

                    $model->_author = $userID;
                }


                $model->attachedFilesUpload = UploadedFile::getInstances($model, 'attachedFilesUpload');
                $model->uploadAttachedFiles();

//
//
                //клон задач
                        switch ($model->deadline_type) {
                            case $model::DEADLINE_TYPE__ONE_TIME:
                                $model->save(false);
                                break;
                            case $model::DEADLINE_TYPE__EVERY_DAY:
                            case $model::DEADLINE_TYPE__EVERY_WEEK:
                            case $model::DEADLINE_TYPE__EVERY_MONTH:
                                $weekDays = $model->deadline_type == $model::DEADLINE_TYPE__EVERY_DAY ? [1,2,3,4,5] : $model->deadline_every_week;
                                $workDays = [1,2,3,4,5];
                                $current = $model->perform_timestamp;
                                $diff = $model->deadline_timestamp - $model->perform_timestamp;

                                //разница рабочих дней
                                $diffwork = 0;
                                $holiday = 0;
                                if (date('d.m.Y', $model->deadline_timestamp) == date('d.m.Y', $model->perform_timestamp)) {
                                    $diffwork = $model->deadline_timestamp - $model->perform_timestamp;
                                }
                                else {
                                    while ($current <= $model->deadline_timestamp) {
                                      $item = date('N',$current);
                                      if (!in_array($item, $workDays) ) {
                                          $holiday ++ ;
                                      }
                                      $current = $current + 60*60*24;
                                    }
                                    $diffwork = $model->deadline_timestamp - $model->perform_timestamp - ($holiday * 60*60*24);
                                }

                                $step = 0;
                                $current = $model->deadline_timestamp;
                                while ($current <= $model->end_timestamp) {
                                  if ($model->deadline_type == $model::DEADLINE_TYPE__EVERY_MONTH) {
                                      $item = date('j',$current);
                                      $weekDays = $model->deadline_every_month;
                                  }
                                  else
                                      $item = date('N',$current);

                                  if (in_array($item, $weekDays) ) {

                                    if ($step == 0) {
                                        //$model->perform_timestamp = $current;
                                        //$model->deadline_timestamp = $current + $diff;
                                        //var_dump(date('d.m.Y H:i', $model->perform_timestamp));echo '<br/>';
                                        //var_dump(date('d.m.Y H:i', $model->deadline_timestamp));echo '<br/>';
                                        //var_dump('first');echo '<br/>';
                                        $model->save(false);
                                    }
                                    else {
                                        $newmodel = new Task();
                                        $newmodel->setScenario($model->deadline_type);
                                        $newmodel->attributes = $model->attributes;
                                        $newmodel->_parent = $model->_id;
                                        $newmodel->_author = $model->_author;
                                        $newmodel->perform_timestamp = $current - $diffwork;

                                        if (date('N',$newmodel->perform_timestamp) == 7) {
                                            //выходной день - воскресенье, то начало - пятница - 2*60*60*24
                                            $newmodel->perform_timestamp = $newmodel->perform_timestamp - (2 * 60*60*24);
                                        }
                                        if (date('N',$newmodel->perform_timestamp) == 6) {
                                            //выходной день - субботу, то начало - пятница - 2*60*60*24
                                            $newmodel->perform_timestamp = $newmodel->perform_timestamp - (1 * 60*60*24);
                                        }
                                        $newmodel->deadline_timestamp = $current;
                                        $newmodel->template = True;
                                        $newmodel->subject = $model->subject.' (периодическая задача)';
                                        //var_dump(date('d.m.Y H:i', $newmodel->perform_timestamp));echo '<br/>';
                                        //var_dump(date('d.m.Y H:i', $newmodel->deadline_timestamp));echo '<br/>';
                                        if ($newmodel->save()) {
                                        }
                                    }
                                    $step++;
                                  }
                                  $current = $current + 60*60*24;                              
                                }

                                break;
                            case $model::DEADLINE_TYPE__EVERY_DATE:
                                if (!is_array($model->deadline_every_date)) {
                                    return false;
                                }
                                
                                $day = $model->deadline_every_date[0];
                                //$model->perform_timestamp = strtotime($day . ' '.date('H:i',strtotime($model->perform_date)));
                                //$model->deadline_timestamp = strtotime($day . ' '.date('H:i',strtotime($model->deadline_date)));
                                $model->save(false);  


                                //разница рабочих дней
                                $current = $model->perform_timestamp;
                                $workDays = [1,2,3,4,5];
                                $diffwork = 0;
                                $holiday = 0;
                                if (date('d.m.Y', $model->deadline_timestamp) == date('d.m.Y', $model->perform_timestamp)) {
                                    $diffwork = $model->deadline_timestamp - $model->perform_timestamp;
                                }
                                else {
                                    while ($current <= $model->deadline_timestamp) {
                                      $item = date('N',$current);
                                      if (!in_array($item, $workDays) ) {
                                          $holiday ++ ;
                                      }
                                      $current = $current + 60*60*24;
                                    }
                                    $diffwork = $model->deadline_timestamp - $model->perform_timestamp - ($holiday * 60*60*24);
                                }


                                //var_dump($model->deadline_every_date);echo '<br/>';
                                foreach ($model->deadline_every_date as $key => $day) {

                                    if ($key == 0) continue;

                                    $newmodel = new Task();
                                    $newmodel->setScenario($model->deadline_type);
                                    $newmodel->attributes = $model->attributes;
                                    $newmodel->_parent = $model->_id;
                                    $newmodel->_author = $model->_author;
                                    $newmodel->subject = $model->subject.' (периодическая задача)';

                                    //$newmodel->perform_timestamp = strtotime($day . ' '.date('H:i',$model->perform_timestamp));
                                    //$newmodel->deadline_timestamp = strtotime($day . ' '.date('H:i',$model->deadline_timestamp));
                                    $current = strtotime($day . ' '.date('H:i',$model->deadline_timestamp));
                                    $newmodel->perform_timestamp = $current - $diffwork;
                                    if (date('N',$newmodel->perform_timestamp) == 7) {
                                            //выходной день - воскресенье, то начало - пятница - 2*60*60*24
                                            $newmodel->perform_timestamp = $newmodel->perform_timestamp - (2 * 60*60*24);
                                    }
                                    if (date('N',$newmodel->perform_timestamp) == 6) {
                                            //выходной день - субботу, то начало - пятница - 2*60*60*24
                                            $newmodel->perform_timestamp = $newmodel->perform_timestamp - (1 * 60*60*24);
                                    }
                                    $newmodel->deadline_timestamp = $current;

                                    $newmodel->template = True;
                                    if ($newmodel->save()) {
                                    } 

                                }

                                
                                break;
                            default:
                                break;
                        }
//
//
               // if ($model->save(false)) {
                    if ($isNewRecord === true) {
                        TaskLog::createNotify($model, TaskLog::NOTIFY_TASK_CREATED, Task::getUserModelId($userID));
                        if ($model->status === Task::STATUS__APPROVAL_AWAITING) {
                            TaskLog::createNotify($model, TaskLog::NOTIFY_TASK_AWAITING_APPROVAL);
                        }
                    } else {
                        $newModelData = $model->toArray($model->listenChangeAttributes());
                        TaskLog::createNotify($model, TaskLog::NOTIFY_TASK_UPDATED, Task::getUserModelId($userID),[
                            'oldModelData' => $oldModelData,
                            'newModelData' => $newModelData,
                        ]);
                    }
                    return $this->redirect(['view', 'id' => (string)$model->_id]);
               // }
            }

        }

        return $this->render($model->isNewRecord ? 'create' : 'update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Task model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $url = $model->getViewUrl(true);
        
    //все дочерние задачи
    $subtasks = Task::find()->where(['_parent' => $model->_id])->all();
    foreach ($subtasks as $subtask) {
      $subtask->delete();
    }
    
    $model->delete();
    
        if (Yii::$app->request->referrer == $url) {
            return $this->redirect(['outbox-done']);
        }
    

              
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the Task model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Task the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     * @throws ForbiddenHttpException Если доступ к модели запрещен
     */
    protected function findModel($id)
    {
        if (($model = Task::findOne($id)) !== null) {
            if ($model->checkAvailableAccess(Yii::$app->getUser()->getId()) === false) {
                throw new ForbiddenHttpException('У вас нет доступа к данной задач');
            }

            return $model;
        } else {
            throw new NotFoundHttpException('Такой задачи не существует');
        }
    }

    public function actionUserAction($id, $type)
    {
        $model = $this->findModel($id);
        if (($action = $model->getUserActionByType($type)) !== null) {
            $action['action']($model);
        }
        return $this->redirect(['view', 'id' => (string)$model->_id]);
    }

    /**
     * Задачи в которых я принимаю участие в определенной роли(согласующий, исполнитель, контролирующий результат) и который ждут от меня действия(ответа)
     *  - от согласующего ждут решения по согласованию
     *  - от исполнителя ждут завершения работы
     *  - от контролирующего проверки результата
     *
     * @return string
     */
    public function actionInboxAwaitingResponse()
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $query = $dataProvider->query;
        /** @var TaskQuery $query */
        $query->inboxAwaitingResponse();

        return $this->render('inbox-awaiting-response', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'displayStatusColumn' => false,
        ]);
    }

    /**
     * Просроченные задачи
     *
     * @return string
     */
    public function actionInboxOverdue()
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $query = $dataProvider->query;
        /** @var TaskQuery $query */
        $query->inboxOverdue();

        return $this->render('inbox-overdue', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'displayStatusColumn' => false,
        ]);
    }

    /**
     * Задачи в которых я исполнитель и который находятся на проверке результата
     *
     * @return string
     */
    public function actionInboxCheck()
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $query = $dataProvider->query;
        /** @var TaskQuery $query */
        $query->inboxCheck();

        return $this->render('inbox-check', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'displayStatusColumn' => false,
        ]);
    }

    /**
     * Задачи в которых я принимаю(принимал) участие в определенной роли(согласующий, исполнитель, контролирующий результат) и которые выполнены
     * 
     * @return string
     */
    public function actionInboxDone()
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $query = $dataProvider->query;
        /** @var TaskQuery $query */
        $query->inboxDone();

        return $this->render('inbox-done', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'displayStatusColumn' => false,
        ]);
    }

    /**
     * Моя задача(я автор задачи) на согласовании и сроки согласования не просрочены
     *
     * @return string
     */
    public function actionOutboxApproving()
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $query = $dataProvider->query;
        /** @var TaskQuery $query */
        $query->outboxApproving();
        return $this->render('outbox-approving', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Моя задача(я автор задачи) выполняется и сроки выполнения не просрочены
     *
     * @return string
     */
    public function actionOutboxPerformed()
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $query = $dataProvider->query;
        /** @var TaskQuery $query */
        $query->outboxPerformed();

        return $this->render('outbox-performed', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Я автор задачи и она просрочена по определенному статус
     *  - просрочен срок согласования
     *  - просрочен срок выполнения
     *  - просрочен срок проверки результата
     *
     * @return string
     */
    public function actionOutboxExpired()
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $query = $dataProvider->query;
        /** @var TaskQuery $query */
        $query->outboxExpired();

        return $this->render('outbox-expired', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Я автор задачи и она выполнена
     *
     * @return string
     */
    public function actionOutboxDone()
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $query = $dataProvider->query;
        /** @var TaskQuery $query */
        $query->outboxDone();

        return $this->render('outbox-done', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Я автор задачи и она находится на проверке
     *
     * @return string
     */
    public function actionOutboxCheck()
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $query = $dataProvider->query;
        /** @var TaskQuery $query */
        $query->outboxCheck();

        return $this->render('outbox-check', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionTest()
    {
        //Task::getNextDeadlineInWeek(strtotime('today'), [1,3], true);
        //echo date('d.m.Y H:i', strtotime('-1day +18hours',strtotime('today')));
        $val = Task::getNextDeadlineInMonth(strtotime('23.08.2016 18:00'), [10, 28]);
        //echo date('d.m.Y H:i:s', $val);
        echo date('d.m.Y H:i:s',strtotime(date('28.m.Y H:i:s', strtotime('23.08.2016 18:00'))));
        //self::getNextDeadlineInWeek(
        //    $begin   = strtotime('today'),
        //    $end     = strtotime('21.09.2016'),
        //    $week    = [1,3],
        //    $weekend = [],
        //    $work    = []
        //);
    }


    /**
     * @param int $begin Дата с которой нужно начать поиск дат
     * @param int $end Дата на которой нужно остановить поиск дат
     * @param array $weekDays Дни недели по которым выполняется задача
     * @param bool $weekend Даты праздников
     * @param bool $works Даты рабочих дней
     * @return bool|mixed
     */
    public static function getNextDeadlineInWeek($begin, $end, $weekDays = [], $weekend = false, $work = false)
    {
        foreach ($weekDays as $weekDay) {
            
        }
    }

    public static function getDeadlineWeekDayTimeStr($value)
    {
        $list = self::deadlineWeeksListForTimeStr();
        if (isset($list[$value])) {
            return $list[$value];
        }
        return $list[$value];
    }

    public static function deadlineWeeksListForTimeStr()
    {
        return [
            1 => 'monday',
            2 => 'tuesday',
            3 => 'wednesday',
            4 => 'thursday',
            5 => 'friday',
            6 => 'saturday',
            0 => 'sunday',
        ];
    }


    public function actionIndexInbox($export = null)
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['scenario' => Task::SCENARIO_INBOX]);

        return $this->render('index-inbox', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndexOutbox($export = null)
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['scenario' => Task::SCENARIO_OUTBOX]);

        return $this->render('index-outbox', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
