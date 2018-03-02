<?php

namespace app\modules\hr\controllers;


use Yii;

use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;

use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use yii\data\ActiveDataProvider;

use app\modules\hr\Module;

use app\modules\hr\models\Employee;
use app\modules\hr\models\EmployeeSearch;
use app\modules\hr\models\File;

use app\modules\hr\models\embedded\Contact;
use app\modules\hr\models\embedded\Education;
use app\modules\hr\models\embedded\Experience;
use app\modules\hr\models\embedded\Family;



/**
 * EmployeeController implements the CRUD actions for Employee model.
 */
class EmployeeController extends Controller
{
    const SELECT2__RESULTS_PER_REQUEST = 10;

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
     * Lists all Employee models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EmployeeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Employee model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Employee model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->formAction(null);
    }

    /**
     * Updates an existing Employee model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        return $this->formAction($id);
    }

    /**
     * @param string|null $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function formAction($id = null)
    {
        $model = new Employee();
        if ($id !== null) {
            $model = $this->findModel($id);
        }
        $data = Yii::$app->request->post();
        if ($model->load($data)) {
            $valid = $model->validate();
            if ($model->identityCard->load($data)) {
                $model->refreshFromEmbedded(); // see https://github.com/yii2tech/embedded#saving-embedded-models-
                $valid = $valid && $model->identityCard->validate();
            }
            if ($model->companyCard->load($data)) {
                $model->refreshFromEmbedded(); // see https://github.com/yii2tech/embedded#saving-embedded-models-
                $valid = $valid && $model->companyCard->validate();
            }
            $valid = $valid && Contact::loadMultipleModelsData($model, 'contacts', $data, true, false);
            $valid = $valid && Education::loadMultipleModelsData($model, 'educations', $data, true, false);
            $valid = $valid && Family::loadMultipleModelsData($model, 'family', $data, true, false);
            $valid = $valid && Experience::loadMultipleModelsData($model, 'experience', $data, true, false);


            $_files = [];
            /** @var File[] $_files */
            if (isset($data['File']) && is_array($data['File'])) {

                foreach ($data['File'] as $fileData) {
                    if (isset($fileData['_id'])) {
                        $fileModel = File::findOne($fileData['_id']);
                        if ($fileModel) {
                            $fileModel->setScenario(File::SCENARIO_UPDATE);
                            $fileModel->load($fileData, '');
                            $valid = $valid && $fileModel->validate();
                            $_files[] = $fileModel;
                        }
                    }
                }
            }

            $model->_files = array_map(function ($file) {
                /** @var File $file */
                return $file->getId(false);
            }, $_files);

            if ($valid) {
                if ($model->save(false)) {
                    foreach ($_files as $file) {
                        $file->save(false);
                    }
                    return $this->redirect($model->getViewUrl());
                }
            }
        }
        return $this->render($model->isNewRecord ? 'create' : 'update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Employee model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionSelect2($term = '')
    {
        Yii::$app->getResponse()->format = Response::FORMAT_JSON;
        $query = Employee::find()->searchByFullName($term);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => self::SELECT2__RESULTS_PER_REQUEST,
            ],
            'sort' => [
                'defaultOrder' => [
                    'last_name' => SORT_ASC,
                    'first_name' => SORT_ASC,
                ],
            ],
        ]);
        return [
            'items' => array_map(function($model) {
                /** @var Employee $model */
                return [
                    'id' => $model->getId(true),
                    'text' => $model->getFullName(),
                ];
            }, $dataProvider->getModels()),
            'total_count' => $dataProvider->getTotalCount(),
        ];
    }

    /**
     * Finds the Employee model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Employee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Employee::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(
                Module::t('employee', 'HTTP_ERROR__NOT_FOUND')
            );
        }
    }

    //public function actionRand()
    //{
    //    //VarDumper::dump($data, 10, true);
    //    for ($i = 0; $i < 250; $i++) {
    //        $data = Json::decode(file_get_contents('http://randus.ru/api.php'));
    //        $model = new Employee();
    //        $model->first_name = $data['fname'];
    //        $model->last_name = $data['lname'];
    //        $model->middle_name = $data['patronymic'];
    //        $model->position = $data['color'];
    //        $model->save(false);
    //    }
    //}
}
