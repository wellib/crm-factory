<?php

namespace app\modules\docs\controllers;

use Yii;
use app\modules\docs\models\Template;
use app\modules\docs\models\TemplateSearch;
use app\modules\docs\models\ContractLog;
use app\modules\accounts\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\Url;
/**
 * TemplateController implements the CRUD actions for Template model.
 */
class TemplateController extends Controller
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
                ],
            ],
        ];
    }

    /**
     * Lists all Template models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Template model.
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
            if (!$model->getAccessUpdate()) {
                return $this->redirect(['view', 'id' => (string)$model->_id]);
            }
        } else {
            $model = new Template();
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
        if ($model->status > 0 ) {
            Yii::$app->session->setFlash('Шаблон уже был утвержден', true);
            return $this->redirect(['view', 'id' => (string)$model->_id]);
        }
        if (!$model->getAccessAccept()) {
            Yii::$app->session->setFlash('Шаблон уже был утвержден', true);
            return $this->redirect(['view', 'id' => (string)$model->_id]);
        }
        $model->status = 1;
        $model->approve = time();
        $model->save(false);
        Yii::$app->session->setFlash('Шаблон утвержден', true);
        return $this->redirect(['view', 'id' => (string)$model->_id]);
        //return $this->redirect(['index']);
    }

    public function actionSendapprove($id)
    {
        $model = $this->findModel($id);
        if ($model->status > 0) {
            Yii::$app->session->setFlash('Шаблон уже был утвержден', true);
            return $this->redirect(['view', 'id' => (string)$model->_id]);
        }

        $user = User::find()->where(['=','status', (string)1])->one();

        $log = \app\modules\docs\models\ContractLog::find()->where(['contract_id' => $model->_id])->orderBy(['_id' => SORT_ASC])->one();

        $message = 'Здравствуйте! Вам отправили договор на утверждение:<br/>
          Ссылка: http://uks-cloud.kz/docs/template/view?id='.(string)$model->_id.'<br/>
          Комментарий: '.(isset($log->comment)?$log->getComment():'').'
        ';

        $this->sendMail($user->email, 'Шаблон на утверждение', $message);

        Yii::$app->session->setFlash('Шаблон отправлен на утверждение', true);
        return $this->redirect(['view', 'id' => (string)$model->_id]);
    }

   public function actionRepeat($id)
    {

        $model = $this->findModel($id);
        if ($model->status != 1) {
            Yii::$app->session->setFlash('Шаблон не был утвержден', true);
            return $this->redirect(['view', 'id' => (string)$model->_id]);
        }
        if (!$model->getAccessRepeatAccept()) {
            Yii::$app->session->setFlash('Шаблон не был утвержден', true);
            return $this->redirect(['view', 'id' => (string)$model->_id]);
        }
        $model->status = 0;
        $model->save(false);

        $userid = $model->_author;
        $user = User::find()->where(['_id' => $userid])->one();

        $log = \app\modules\docs\models\ContractLog::find()->where(['contract_id' => $model->_id])->orderBy(['_id' => SORT_ASC])->one();

        $message = 'Здравствуйте! Вам отправили созданный вами шаблон на доработку:<br/>
          Ссылка: http://uks-cloud.kz/docs/template/view?id='.(string)$model->_id.'<br/>
          Комментарий: '.(isset($log->comment)?$log->getComment():'').'
        ';

        $this->sendMail($user->email, 'Шаблон на переработку', $message);

        Yii::$app->session->setFlash('Шаблон на переработку', true);
        return $this->redirect(['view', 'id' => (string)$model->_id]);
        //return $this->redirect(['index']);
    }
    /**
     * Finds the Template model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $_id
     * @return Template the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Template::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionDownloadAttachedFile($id, $filename, $type = null)
    {
        $model = $this->findModel($id);
        $allFiles = $model->getAttachedFilesPaths();

        if (!isset($allFiles[$filename]) || !file_exists($allFiles[$filename]['file_path'])) {
            throw new NotFoundHttpException('Файл не найден, возможно он был удален');
        }
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
