<?php

namespace app\modules\structure\controllers;

use Yii;

use yii\web\Controller;
use yii\web\NotFoundHttpException;

use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use app\modules\structure\Module;
use app\modules\structure\models\Department;
//use app\modules\structure\models\DepartmentSearch;

/**
 * DepartmentController implements the CRUD actions for Department model.
 */
class DepartmentController extends Controller
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
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [Module::ROLE_NAME],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Department models.
     * @return mixed
     */
    //public function actionIndex()
    //{
    //    $searchModel = new DepartmentSearch();
    //    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    //
    //    return $this->render('index', [
    //        'searchModel' => $searchModel,
    //        'dataProvider' => $dataProvider,
    //    ]);
    //}

    public function actionTree()
    {
        return $this->render('tree');
    }

    /**
     * Displays a single Department model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Department model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->formAction(null);
    }

    /**
     * Updates an existing Department model.
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
        if ($id !== null) {
            $model = $this->findModel($id);
        } else {
            $model = new Department();
        }

        if ($model->load(Yii::$app->request->post())) {
            $valid = $model->validate();
            if ($valid) {
                if ($model->save(false)) {
                    //$this->redirect(['view', 'id' => $model->getId(true)]);
                    $this->redirect(['tree']);
                }
            }
        }
        
        return $this->render($model->isNewRecord ? 'create' : 'update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Department model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['tree']);
    }

    /**
     * Finds the Department model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Department the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Department::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Module::t('department', 'HTTP_ERROR__NOT_FOUND'));
        }
    }
}
