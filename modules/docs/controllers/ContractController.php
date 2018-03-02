<?php

namespace app\modules\docs\controllers;

use Yii;
use app\modules\docs\models\Contract;
use app\modules\docs\models\ContractLog;
use app\modules\docs\models\ContractSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\Url;
use app\modules\accounts\models\User;

/**
 * ContractController implements the CRUD actions for Contract model.
 */
class ContractController extends Controller
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
     * Lists all Contract models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ContractSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
		}

    public function actionTree()
    {
        $searchModel = new ContractSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

				// returns an array of Post objects
				$contracts = $dataProvider->getModels();


				$contracts_array = array();
				foreach ($contracts as $contract){
						$id = $contract->getId();
						$parent = (isset($contract->parent) && $contract->parent!='')?$contract->parent:0;

						$contracts_array[$parent][] = $contract;
						//$contracts_array[$id]['name'] = $contract->name;
						//$contracts_array[$id]['parent'] = $contract->parent;
				}

				/*$rootId = 0;
				foreach ($contracts_array as $id => $node) {
					if ($node['parent']) {
						$contracts_array[$node['parent']]['sub'][$id] =& $contracts_array[$id];
					} else {
						$rootId = $id;
					}
				}
				$return = (array($rootId => $contracts_array[$rootId]));
				*/
        return $this->render('tree', [
            'return' => $contracts_array,
        ]);

		}

    /**
     * Displays a single Contract model.
     * @param integer $_id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionView2($id)
    {
        return $this->render('view2', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Contract model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
         return $this->formAction(null);
    }

    /**
     * Updates an existing Contract model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $_id
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
            $userID = Yii::$app->getUser()->getId();
						//if ($model->status > 0 && $userID != '57bec929e1367a03a85f2a41') {
						if (!$model->getAccessUpdate()) {
								return $this->redirect(['view', 'id' => (string)$model->_id]);
						}
        } else {
            $model = new Contract();
						$model->alls = 1;
        }

        if ($model->load(Yii::$app->request->post())){
            if ($model->validate()) {
                $userID = Yii::$app->getUser()->getId();
                if ($model->isNewRecord) {
                    $model->_author = $userID;
                }

                $model->attachedFilesUpload = UploadedFile::getInstances($model, 'attachedFilesUpload');
                $model->uploadAttachedFiles();
                
                if ($model->save(false)) {
                    return $this->redirect(['view', 'id' => (string)$model->_id]);
                }
            }
        }

        return $this->render($model->isNewRecord ? 'create' : 'update', [
            'model' => $model,
        ]);
    }
	
    /**
     * Deletes an existing Contract model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $_id
     * @return mixed
     */
    public function actionDelete($id)
    {

        $model = $this->findModel($id);
				if (!$model->getAccessUpdate()) {
						return $this->redirect(['view', 'id' => (string)$model->_id]);
				}

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionApprove($id)
    {
        $model = $this->findModel($id);
				if ($model->status > 0) {
						Yii::$app->session->setFlash('Договор уже был утвержден', true);
						return $this->redirect(['view', 'id' => (string)$model->_id]);
				}
				if (!$model->getAccessAccept()) {
						Yii::$app->session->setFlash('Договор уже был утвержден', true);
						return $this->redirect(['view', 'id' => (string)$model->_id]);
				}
        $model->status = 1;
				$model->approve = time();
        $model->save(false);

				Yii::$app->session->setFlash('Договор утвержден', true);
				return $this->redirect(['view', 'id' => (string)$model->_id]);
        //return $this->redirect(['index']);
    }

    public function actionSendapprove($id)
    {
        $model = $this->findModel($id);
				if ($model->status > 0) {
						Yii::$app->session->setFlash('Договор уже был утвержден', true);
						return $this->redirect(['view', 'id' => (string)$model->_id]);
				}

        $user = User::find()->where(['=','status', (string)1])->one();

				$log = \app\modules\docs\models\ContractLog::find()->where(['contract_id' => $model->_id])->orderBy(['_id' => SORT_ASC])->one();

				$message = 'Здравствуйте! Вам отправили договор на утверждение:<br/>
					Ссылка: http://uks-cloud.kz/docs/contract/view?id='.(string)$model->_id.'<br/>
					Комментарий: '.(isset($log->comment)?$log->getComment():'').'
				';

				$this->sendMail($user->email, 'Договор на утверждение', $message);

				Yii::$app->session->setFlash('Договор отправлен на утверждение', true);
				return $this->redirect(['view', 'id' => (string)$model->_id]);
    }

    public function actionRepeat($id)
    {

        $model = $this->findModel($id);
				if ($model->status != 1) {
						Yii::$app->session->setFlash('Договор не был утвержден', true);
						return $this->redirect(['view', 'id' => (string)$model->_id]);
				}
				if (!$model->getAccessRepeatAccept()) {
						Yii::$app->session->setFlash('Договор не был утвержден', true);
						return $this->redirect(['view', 'id' => (string)$model->_id]);
				}
        $model->status = 0;
        $model->save(false);

				$userid = $model->_author;
        $user = User::find()->where(['_id' => $userid])->one();

				$log = \app\modules\docs\models\ContractLog::find()->where(['contract_id' => $model->_id])->orderBy(['_id' => SORT_ASC])->one();

				$message = 'Здравствуйте! Вам отправили созданный вами договор на доработку:<br/>
					Ссылка: http://uks-cloud.kz/docs/contract/view?id='.(string)$model->_id.'<br/>
					Комментарий: '.(isset($log->comment)?$log->getComment():'').'
				';

				$this->sendMail($user->email, 'Договор на переработку', $message);

				Yii::$app->session->setFlash('Договор на переработку', true);
				return $this->redirect(['view', 'id' => (string)$model->_id]);
        //return $this->redirect(['index']);
    }

    /**
     * Finds the Contract model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $_id
     * @return Contract the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Contract::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionDownloadAttachedFile($id, $filename, $type = null)
    {
				$model = $this->findModel($id);
				//if (!$model->checkAvailableAccess(Yii::$app->getUser()->getId())) {
				//		throw new ForbiddenHttpException('У вас нет доступа к данному файлу');
				//}
        $allFiles = $model->getAttachedFilesPaths();

        if (!isset($allFiles[$filename]) || !file_exists($allFiles[$filename]['file_path'])) {
            throw new NotFoundHttpException('Файл не найден, возможно он был удален');
        }
        //$filename = $allFiles[$filename];
        header("Content-Length: " . filesize($allFiles[$filename]['file_path']));
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $allFiles[$filename]['filename_orig']);
        readfile($allFiles[$filename]['file_path']);
    }

    public function sendMail($email, $subject, $body)
    {
        return Yii::$app->mailer->compose()
                ->setFrom(Yii::$app->params['smtpEmail'])
                ->setFrom([Yii::$app->params['smtpEmail'] => Yii::$app->name])
                ->setTo($email)
                ->setSubject($subject)
                ->setTextBody(strip_tags($body))
                ->setHtmlBody($body)
                ->send();
    }

    public function actionSendComment($id)
    {
        $task = $this->findModel($id);
        $model = new ContractLog();
        //$model->setScenario(TaskLog::SCENARIO_COMMENT);
        if ($model->load(Yii::$app->request->post()) ) {
            $model->contract_id = $task->_id;
            $model->_user = Yii::$app->getUser()->getIdentity()->_id;
            $model->type = ContractLog::COMMENT;
            $model->attachedFilesUpload = UploadedFile::getInstances($model, 'attachedFilesUpload');
            $model->uploadAttachedFiles();
            if (!empty($model->comment)) {
                $model->save(false);
            }
            $this->actionRepeat($id);
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderPartial('log/_comment-form', [
                'model' => new ContractLog(),
                'contract_id' => $id,
            ]);
        } else {
            return $this->redirect(['view2', 'id' => $id]);
        }
    }

}
