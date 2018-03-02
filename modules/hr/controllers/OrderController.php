<?php

namespace app\modules\hr\controllers;

use Yii;


use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use app\modules\hr\Module;
use app\modules\hr\models\Order;
use app\modules\hr\models\OrderSearch;


/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
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
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Order model.
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
     * Creates a new Order model.
     * @param $type
     * @return string|\yii\web\Response
     */
    public function actionCreate($type)
    {
        return $this->formAction(null, $type);
    }


    /**
     * @param string $id
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id)
    {
        return $this->formAction($id);
    }

    protected function formAction($id = null, $type = null)
    {
        if ($id !== null) {
            $model = $this->findModel($id);
        } else {
            $model = new Order();
            if (Order::typeIsExist($type) === false) {
                throw new BadRequestHttpException('GET parameter "type" is invalid');
            }
            $model->type = $type;
        }
        $postData = Yii::$app->request->post();
        if ($model->load($postData)) {
            if ($embeddedModel = $model->getEmbeddedModelByType()) {
                $embeddedModel->load($postData);
            }
            $valid = $model->validate();
            if ($valid) {
                if ($model->save(false)) {
                    return $this->redirect($model->getViewRoute());
                }
            }
        }

        return $this->render($model->isNewRecord ? 'create' : 'update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Module::t('order', 'HTTP_ERROR__NOT_FOUND'));
        }
    }
}
