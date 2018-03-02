<?php

namespace backend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\Services;
use common\models\ServiceNames;
use common\models\Lang;
use common\models\ServiceVariants;
use common\models\ServiceVariantNames;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;




/**
 * ServicesController implements the CRUD actions for Services model.
 */
class ServicesController extends Controller
{
    /**
     * @inheritdoc
     */

    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return \common\models\User::isUserAdmin(Yii::$app->user->identity->u_login);
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //'delete' => ['POST'],
                ],
            ],
        ];
    }
    /**
     * Lists all Services models.
     * @return mixed
     */
    public function actionIndex($id = null)
    {
        
        if ($id) {
            $language = Lang::findOne(['id' => $id]);
        }
        else {
            $language = Lang::defaultLang();
        }
    
        $languages = Lang::find()->where('id != :sel', ['sel' => $language['id']])->all();
    
        $model = new Services();
        
        $search_details = Yii::$app->request->get('Services');
        $search_details['lang'] = $language['id'];
        $dataProvider = $model->search($search_details);
        
        if ($search_details) {
            $model->attributes = $search_details;
        }
        $dataProvider->pagination->pageSize=Yii::$app->request->get('page_size');
    $model->lang = $language['id'];
        return $this->render('services', [
            'searchModel' => $model,
            'dataProvider' => $dataProvider,
            'languages' => $languages,
            'language' => $language
        ]);
    }

    public function actionView($id = null, $lang = null){
    
        if ($lang) {
            $language = Lang::findOne(['id' => $lang]);
        }
        else {
            $language = Lang::defaultLang();
            $lang = $language['id'];
        }

        if($id) {     
            $model = Services::findOne($id);    
            if(!$model) {
                Yii::$app->session->setFlash('alert', ['type' => 'error', 'message' => 'Page not found']);
                $this->redirect(['Services']);
            } 
            
            /* if($model->lang && !$lang) {
                $lang = $model->pLang;
            }*/
            
            $pcontent = ServicesContent::find()->where(['id' => $id])->andWhere(['lang' => $lang])->one();
            
            if(!Yii::$app->request->isPost ) { 
            if ($pcontent) {
                $model->setAttribute('content', $pcontent->getAttribute('content'));
                $model->setAttribute('name', $pcontent->getAttribute('name'));
            } else {
                $model->setAttribute('content', ''); 
                $model->setAttribute('name', '');             
            }
            }
        }
        else {
            $model = new Services();
        }
        


        $language = Lang::findOne(['id' => $lang]);
        $Lang = Lang::find()->where('id != :sel', ['sel' => $lang])->all();
 
 
        
        if (!$language) {
            Yii::$app->session->setFlash('alert', ['type' => 'error', 'message' => 'Wrong language']);
            return $this->redirect(['']);
        }
        
        $Lang = Lang::find()->where('id != :sel', ['sel' => $lang])->all();
        
        $languages = Lang::find()->where('id != :sel', ['sel' => $lang])->all();
        
        if ($model->load(Yii::$app->request->post())) {
                
           // $model->setAttribute('modified', date('Y-m-d H:i:s'));
            $model->save(false);
            
            if(!$id || ($id && !$pcontent)) {
                $pcontent = new ServicesContent();
                $pcontent->setAttribute('id', $model->getPrimaryKey());
                $pcontent->setAttribute('lang', $model->getAttribute('lang'));
            }            
            
            $pcontent->setAttribute('content', 'ggggggggggggggggg');
            $pcontent->setAttribute('name', $model->getAttribute('name'));
            
            
            $pcontent->save();
             
            Yii::$app->session->setFlash('alert', ['type' => 'success', 'message' => 'Page added successfully']);
            //return $this->redirect(['/services']);
        }
        
        return $this->render('view', [
            'model' => $model,
            'Lang' => $Lang,
            'languages' => $languages,
            'language' => $language,
            'selected_language' => $lang,
            'pickWhatValues' => Services::$sPickWhatValues,
        ]);
    }
 
 


    /**
     * Deletes an existing Services model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete0($id = null){
        
        if (!$id) { 
            Yii::$app->session->setFlash('alert', ['type' => 'info', 'message' => 'Page not found']);
            return $this->redirect(['/services']);
        }
        
        $model = Services::findOne($id);
        
        if (!$model) { 
            Yii::$app->session->setFlash('alert', ['type' => 'info', 'message' => 'Page not found']);
            return $this->redirect(['/services']);
        }
        
        ServiceNames::deleteAll(['sId' => $id]);
        
        if($model->delete()) {
            Yii::$app->session->setFlash('alert', ['type' => 'info', 'message' => 'Page has been deleted']);
            return $this->redirect(['/services']);
        }
    }


   public function actionServices($lang = null) {
        
        
    if ($lang) {
            $language = Lang::findOne(['id' => $lang]);
        }
        else {
            $language = Lang::defaultLang();
            $lang = $language['id'];
        }
    
        $languages = Lang::find()->where('id != :sel', ['sel' => $lang])->all();
        
        $model = new ServiceNames();
        
        $search_details = Yii::$app->request->get('ServiceNames');
        $search_details['lang'] = $language['id'];
        $dataProvider = $model->search($search_details);
        
        if ($search_details) {
            $model->attributes = $search_details;
        }
        $dataProvider->pagination->pageSize=Yii::$app->request->get('page_size');
        
        return $this->render('services', [
            'searchModel' => $model,
            'dataProvider' => $dataProvider,
            'languages' => $languages,
            'language' => $language
        ]);
    }
    
    public function actionService($id = null, $lang = null){
        
        if ($lang) {
            $language = Lang::findOne(['id' => $lang]);
        }
        else {
            $language = Lang::defaultLang();
        }
        
        if (!$language) {
            Yii::$app->session->setFlash('alert', ['type' => 'error', 'message' => 'Неправильный язык']);
            return $this->redirect(['services']);
        }
        
        if(!$id) {
            
            $service = new Services();
            $service->sEnabled = 0;
            $service->sPickWhat = 0;
            
            $languages = Lang::find()->all();
            
            if($service->save()) {
                foreach ($languages as $l) {
                    $model = new ServiceNames();
                    $model->lang = $l['id'];
                    $model->sDesc = ' ';
                    $model->sId = $service->sId;
                    $model->sName = 'Новая услуга';
                    $model->save(false);
                }
                
                return $this->redirect(['/services/service/', 'id' => $service['sId'], 'lang' => $language['id']]);
            }
            
            Yii::$app->session->setFlash('alert', ['type' => 'error', 'message' => 'Ошибка при создании услуги']);
            return $this->redirect(['services']);
        }
        
        $languages = Lang::find()->where('id != :sel', ['sel' => $language['id']])->all();
        $service = Services::findOne($id);
        
        if(!$service) {
            Yii::$app->session->setFlash('alert', ['type' => 'error', 'message' => 'Услуга не найдена']);
            return $this->redirect(['services']);
        }
        
        $serviceName = $service->getName($language['id']);
        if(!$serviceName) {
            $serviceName = new ServiceNames();
            $serviceName->sId = $service['sId'];
            $serviceName->lang = $language['id'];
        }
        
        if (Yii::$app->request->isPost) {

            $data = Yii::$app->request->post();
            
            $service->sEnabled = (isset($data['Services']['sEnabled']) && $data['Services']['sEnabled'] == 'on') ? '1' : '0';
            $service->sPickWhat = (isset($data['Services']['sPickWhat'])) && isset(Services::$sPickWhatValues[$data['Services']['sPickWhat']]) ? $data['Services']['sPickWhat'] : 0;
            $service->slug = (isset($data['Services']['slug'])) ? $data['Services']['slug'] : '';
            $service->save();
            
            if($serviceName->load($data) && $serviceName->save()) {
                Yii::$app->session->setFlash('alert', ['type' => 'success', 'message' => 'Услуга обновлена']);
                return $this->redirect(['services/', 'id' => $language['id']]);
            }
        }
        
        return $this->render('service', [
            'service' => $service,
            'serviceName' => $serviceName,
            'pickWhatValues' => Services::$sPickWhatValues,
            'variants' => ServiceVariants::find()->where(['sId' => $service['sId']])->all(),
            'languages' => $languages,
            'language' => $language
        ]);
    }
    
    public function actionDelete($id){
        
        if(!$id) {
            Yii::$app->session->setFlash('alert', ['type' => 'error', 'message' => 'Service not found']);
            return $this->redirect(['/services']);
        }
        
        $model = Services::findOne(['sId' => $id]);
        
        ServiceNames::deleteAll(['sId' => $id]);
        $variants = ServiceVariants::find()->where(['sId' => $id])->all();
        foreach ($variants as $variant) {
            ServiceVariantNames::deleteAll(['svId' => $variant->svId]);
            $variant->delete();
        }
        
        if($model->delete()) {
            Yii::$app->session->setFlash('alert', ['type' => 'success', 'message' => 'Service has been deleted']);
        }
        else {
            Yii::$app->session->setFlash('alert', ['type' => 'error', 'message' => 'Service not found']);
        }
        
        return $this->redirect(['/services']);
    }

}
