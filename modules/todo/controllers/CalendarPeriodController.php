<?php

namespace app\modules\todo\controllers;

use app\modules\todo\models\Task;
use Yii;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\todo\Module;
use app\modules\todo\models\CalendarPeriod;
use app\modules\todo\models\CalendarPeriodSearch;

/**
 * CalendarPeriodController implements the CRUD actions for CalendarPeriod model.
 */
class CalendarPeriodController extends Controller
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
     * Lists all CalendarPeriod models.
     * @return mixed
     */
    public function actionIndex($month = null, $year = null)
    {
        $searchModel = new CalendarPeriodSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);



        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
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
        $model = CalendarPeriod::find()->where([
            'from_date' => $ts,
            'to_date' => $ts,
        ])->one();
        if (!$model) {
            $model = new CalendarPeriod();
            $model->fromDateFormat = $_POST['date'];
            $model->toDateFormat = $_POST['date'];
            $model->name = '1 day';
        }
        $model->type = $_POST['type'];
        $model->save();
        //var_dump($model->getErrors());
    }

    /**
     * Displays a single CalendarPeriod model.
     * @param mixed $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CalendarPeriod model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CalendarPeriod();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => (string)$model->_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing CalendarPeriod model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param mixed $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => (string)$model->_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing CalendarPeriod model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param mixed $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CalendarPeriod model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param mixed $id
     * @return CalendarPeriod the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CalendarPeriod::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Module::t('calendar_period', 'HTTP_ERROR__NOT_FOUND'));
        }
    }

    public function actionTest()
    {
        VarDumper::dump(CalendarPeriod::listByType(CalendarPeriod::TYPE_HOLIDAYS), 10, true);
        VarDumper::dump(CalendarPeriod::listByType(CalendarPeriod::TYPE_WORKDAYS), 10, true);
    }
}
