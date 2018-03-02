<?php

namespace app\modules\docs\controllers;

use Yii;
use app\modules\docs\models\Calendar;
use app\modules\docs\models\CalendarSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CalendarController implements the CRUD actions for Calendar model.
 */
class CalendarController extends Controller
{
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
        ];
    }

    /**
     * Lists all Calendar models.
     * @return mixed
     */
    public function actionIndex()
    {
    $monthes = array(
      1 => 'Январь' , 2 => 'Февраль' , 3 => 'Март' ,
      4 => 'Апрель' , 5 => 'Май' , 6 => 'Июнь' ,
      7 => 'Июль' , 8 => 'Август' , 9 => 'Сентябрь' ,
      10 => 'Октябрь' , 11 => 'Ноябрь' ,
      12 => 'Декабрь'
    );

    $calendar_event_types=array();
    $calendar_event_types = array (
      array(
        'type' => 'alert','color' => 'yellow','name' => 'Предупреждение'
      ),
    );

    $searchModel = new CalendarSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    $models = $dataProvider->getModels();
    $array_models = array();
    foreach ($models as $id => $event){
      $array_models[$event->date] = $id;
    }
    
    $model = new Calendar();

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
          return $this->redirect(['index', 'id' => (string)$model->_id]);
    }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
            'models' => $models,
            'monthes' => $monthes,
            'calendar_event_types' => $calendar_event_types,  
            'array_models' => $array_models,      
        ]);
    
    }

    /**
     * Displays a single Calendar model.
     * @param integer $_id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Calendar model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Calendar();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => (string)$model->_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Calendar model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $_id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => (string)$model->_id]);
        } else {

        if (Yii::$app->request->isAjax) {
            $this->layout = 'clean'; 
            return $this->render('update', [
                'model' => $model,
            ]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
        }
    }

    /**
     * Deletes an existing Calendar model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $_id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Calendar model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $_id
     * @return Calendar the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Calendar::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }



    public function actionDay($month = null, $year = null)
    {
  $monthes = array(
      1 => 'Январь' , 2 => 'Февраль' , 3 => 'Март' ,
      4 => 'Апрель' , 5 => 'Май' , 6 => 'Июнь' ,
      7 => 'Июль' , 8 => 'Август' , 9 => 'Сентябрь' ,
      10 => 'Октябрь' , 11 => 'Ноябрь' ,
      12 => 'Декабрь'
    );

    $calendar_event_types=array();
    $calendar_event_types = array (
      array(
        'type' => 'alert','color' => 'yellow','name' => 'Предупреждение'
      ),
    );

        $searchModel = new CalendarSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    $models = $dataProvider->getModels();
    $array_models = array();
    foreach ($models as $id => $event){
      $array_models[$event->date] = $id;
    }
    
        $model = new Calendar();

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
          return $this->redirect(['day', 'id' => (string)$model->_id]);
    }

        return $this->render('day', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
      'model' => $model,
      'models' => $models,
      'monthes' => $monthes,
      'calendar_event_types' => $calendar_event_types,  
      'array_models' => $array_models,        
        ]);
    }

    public function actionWeek($month = null, $year = null)
    {
    
    $monthes = array(
      1 => 'Январь' , 2 => 'Февраль' , 3 => 'Март' ,
      4 => 'Апрель' , 5 => 'Май' , 6 => 'Июнь' ,
      7 => 'Июль' , 8 => 'Август' , 9 => 'Сентябрь' ,
      10 => 'Октябрь' , 11 => 'Ноябрь' ,
      12 => 'Декабрь'
    );

    $calendar_event_types=array();
    $calendar_event_types = array (
      array(
        'type' => 'alert','color' => 'yellow','name' => 'Предупреждение'
      ),
    );

        $searchModel = new CalendarSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    $models = $dataProvider->getModels();
    $array_models = array();
    foreach ($models as $id => $event){
      $array_models[$event->date] = $id;
    }
    
        $model = new Calendar();

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
          return $this->redirect(['week', 'id' => (string)$model->_id]);
    }

        return $this->render('week', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
      'model' => $model,
      'models' => $models,
      'monthes' => $monthes,
      'calendar_event_types' => $calendar_event_types,  
      'array_models' => $array_models,        
        ]);
    }

    public function actionMonth($month = null, $year = null)
    {
    
    $monthes = array(
      1 => 'Январь' , 2 => 'Февраль' , 3 => 'Март' ,
      4 => 'Апрель' , 5 => 'Май' , 6 => 'Июнь' ,
      7 => 'Июль' , 8 => 'Август' , 9 => 'Сентябрь' ,
      10 => 'Октябрь' , 11 => 'Ноябрь' ,
      12 => 'Декабрь'
    );

    $calendar_event_types=array();
    $calendar_event_types = array (
      array(
        'type' => 'alert','color' => 'yellow','name' => 'Предупреждение'
      ),
    );

        $searchModel = new CalendarSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    $models = $dataProvider->getModels();
    $array_models = array();
    foreach ($models as $id => $event){
      $array_models[$event->date] = $id;
    }
    
        $model = new Calendar();

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
          return $this->redirect(['index', 'id' => (string)$model->_id]);
    }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
      'model' => $model,
      'models' => $models,
      'monthes' => $monthes,
      'calendar_event_types' => $calendar_event_types,  
      'array_models' => $array_models,        
        ]);
    }

  public function actionAjax($month = null, $year = null)
    {

    if(isset($_REQUEST['day_inc'])){
      $_SESSION['curtime']+=86400*$_REQUEST['day_inc'];
      echo $_SESSION['curtime'];
      /*
      $m=intval(date("m"));
      $m++;
      $y=intval(date("y"));
      if($m<12 && $m>0)$m+=$_REQUEST['month_inc'];
      else{
        $m=1;
        $y++;
      }
      $_SESSION[curtime]+=strtotime(date("01-m-Y",$_SESSION[curtime]));/**/
      exit;
    }
    if($_REQUEST['week_inc']){
      $_SESSION['curtime']+=7*86400*$_REQUEST['week_inc'];
      echo $_SESSION['curtime'];
      /*
      $m=intval(date("m"));
      $m++;
      $y=intval(date("y"));
      if($m<12 && $m>0)$m+=$_REQUEST['month_inc'];
      else{
        $m=1;
        $y++;
      }
      $_SESSION[curtime]+=strtotime(date("01-m-Y",$_SESSION[curtime]));/**/
      exit;
    }

    if($_REQUEST[addevent]){
      $dt=strtotime($_REQUEST[dt]);
      if($_REQUEST['eventid']){ // редактирование
        $pdo->prepare('UPDATE `calendar` set `dt`=?, `tm`=?, `name`=?, `descr`=?,`type`=? where id=?')
          ->execute(array(
            date('Y-m-d',$dt),
            date('H:i',$dt),
            $_REQUEST['name'],
            $_REQUEST['descr'],
            $_REQUEST['type'],
            $_REQUEST['eventid']
          ));      
      }else{ // добавление
        $pdo->prepare('INSERT INTO `calendar` set `user`=?, `dt`=?, `tm`=?, `name`=?, `descr`=?,`type`=?')
          ->execute(array(
            $_SESSION[user][id],
            date('Y-m-d',$dt),
            date('H:i',$dt),
            $_REQUEST['name'],
            $_REQUEST['descr'],
            $_REQUEST['type']
          ));    
      }

    }

    if($_REQUEST[delevent]){
      $pdo->query("delete from calendar where id=".$_REQUEST[delevent]);
      exit;
    }
    }
  
    public function actionCalendar($month = null, $year = null)
    {
        $month = empty($month) ? date('m') : $month;
        $year  = empty($year)  ? date('Y') : $year;
        
        return $this->render('calendar', [
            'month' => $month,
            'year'  => $year,
        ]);
    }

    public function actionCalendarUp()
    {
        //var_dump($_POST['date']);
        $ts = strtotime($_POST['date']);
        $model = Calendar::find()->where([
            'from_date' => $ts,
            'to_date' => $ts,
        ])->one();
        if (!$model) {
            $model = new Calendar();
            $model->fromDateFormat = $_POST['date'];
            $model->toDateFormat = $_POST['date'];
            $model->name = '1 day';
        }
        $model->type = $_POST['type'];
        $model->save();
        //var_dump($model->getErrors());
    }



    public function actionSearch($month = null, $year = null)
    {
    
  $monthes = array(
      1 => 'Январь' , 2 => 'Февраль' , 3 => 'Март' ,
      4 => 'Апрель' , 5 => 'Май' , 6 => 'Июнь' ,
      7 => 'Июль' , 8 => 'Август' , 9 => 'Сентябрь' ,
      10 => 'Октябрь' , 11 => 'Ноябрь' ,
      12 => 'Декабрь'
    );

    $calendar_event_types=array();
    $calendar_event_types = array (
      array(
        'type' => 'alert','color' => 'yellow','name' => 'Предупреждение'
      ),
    );

        $searchModel = new CalendarSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    $models = $dataProvider->getModels();
    $array_models = array();
    foreach ($models as $id => $event){
      $array_models[$event->date] = $id;
    }
    
        $model = new Calendar();

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
          return $this->redirect(['day', 'id' => (string)$model->_id]);
    }



        return $this->render('search', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
      'model' => $model,
      'models' => $models,
      'monthes' => $monthes,
      'calendar_event_types' => $calendar_event_types,  
      'array_models' => $array_models,        
        ]);


    }
}
