<?php

namespace app\modules\accounts\controllers\backend;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\accounts\models\User;
use app\modules\accounts\models\backend\UserSearch;
use yii\web\UploadedFile;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->checkAccess();
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->formAction(null);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        return $this->formAction($id);
    }

    /**
     * @param null $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    protected function formAction($id = null)
    {
        $model = new User();
        $model->setScenario(User::SCENARIO_CREATE);
        if ($id !== null) {
            $model = $this->findModel($id);
            $model->setScenario(User::SCENARIO_UPDATE);
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->avatar_upload = UploadedFile::getInstance($model, 'avatar_upload');
            if ($model->save()) {
                return $this->redirect(['view', 'id' => (string)$model->_id]);
            }
        }
        return $this->render($model->isNewRecord ? 'create' : 'update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function checkAccess()
    {
        if (Yii::$app->getUser()->getIdentity()->nickname !== 'root') {
            $this->redirect(Yii::$app->request->referrer);
        }
    }
}
