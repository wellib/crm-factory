<?php

namespace backend\controllers;

use Yii;
use yii\web\Response;
use common\models\Sales;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;

/**
 * SalesController implements the CRUD actions for Sales model.
 */
class SalesController extends Controller
{
    
    
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Sales models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Sales::find(),
            'sort'=> ['defaultOrder' => ['sId'=>SORT_DESC]]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Sales model.
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
     * Creates a new Sales model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($good = NULL, $item = NULL, $destination = NULL)
    {
        $model = new Sales();

        $data = ArrayHelper::map(\common\models\Tracks::find()->all(),'tId','tShowName');
        
        if ($good && $item) {
            if ($good == 1) {
                $model->sGoodWhat = 1;
                $model->sGood = $item;
                $data = ArrayHelper::map(\common\models\Albums::find()->all(),'aId','aName');
            } 
            elseif ($good == 2) {
                $model->sGoodWhat = 0;
                $model->sGood = $item;
            }
        }

        $model->sDate = date('d.m.Y');

        if ($model->load(Yii::$app->request->post()) ) {
            
            $model->sSumm =$model->sQty * $model->sPrice;
            $model->sAddDate = date('d.m.Y');
            $model->sAddTime = date('h:i:s');

            
            if ($model->save() ) {
                
                $uid = 0;
                
                if($model->sGoodWhat==0){
                    $find = $model->hasOne(\common\models\Tracks::className(), ['tId' => 'sGood'])->one();
                    $uid= isset($find->tName)?$find->tUser:0;
                }
                if($model->sGoodWhat==1){
                    $find = $model->hasOne(\common\models\Albums::className(), ['aId' => 'sGood'])->one();
                    $uid = isset($find->aName)?$find->aUser:0;
                }
                if($model->sGoodWhat==2){
                    $find = $model->hasOne(\common\models\Books::className(), ['bId' => 'sGood'])->one();
                    $uid = isset($find->bName)?$find->bUser:0;
                } 
    
                if ($uid) {
                    $modelBalance = new \common\models\Balance();
                    $modelBalance->acUser = $uid;
                    $modelBalance->acTs = time(); 
                    $modelBalance->acDate = date("d.m.Y");
                    $modelBalance->acTime = date("h:i:s");
                    $modelBalance->acPrih = $model->sSumm;
                    $modelBalance->acRash = 0;            
                    $modelBalance->acDesc = 'Royalty';  
                            
                    $modelBalance->save(); 
                    
                    $modelSalesPayed = new \common\models\SalesPayed();
                    $modelSalesPayed->spid = $modelBalance->acId;
                    $modelSalesPayed->sId = $model->sId; 
                    $modelSalesPayed->sum = $model->sSumm;
       
                    $modelSalesPayed->save();    
                }
                
                if ($destination)
                    return $this->redirect([$destination]);
                else
                    return $this->redirect(['index', 'id' => $model->sId]);
            }
            else 
                return $this->render('create', [
                    'model' => $model,
                ]);                
        } else {
            return $this->render('create', [
                'model' => $model,
                'data' => $data
            ]);
        }
    }


    /**
     * Creates a new Sales model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAjaxCreate($good = NULL, $item = NULL, $destination = NULL)
    {
        $this->layout = 'clean';
        $model = new Sales();

        //$this->performAjaxValidation($model);
        
        $data = ArrayHelper::map(\common\models\Tracks::find()->all(),'tId','tShowName');
        
        if ($good && $item) {
            if ($good == 1) {
                $model->sGoodWhat = 1;
                $model->sGood = $item;
                //$data = ArrayHelper::map(\common\models\Albums::find()->all(),'aId','aName');
                $datamodel = \common\models\Albums::find()->where(['=','aId',$item])->one();
                if (isset($datamodel->aId))
                    $data = [$datamodel->aId => $datamodel->aName];
            } 
            elseif ($good == 2) {
                $model->sGoodWhat = 0;
                $model->sGood = $item;
                
                $datamodel = \common\models\Tracks::find()->where(['=','tId',$item])->one();
                if (isset($datamodel->tId))
                    $data = [$datamodel->tId => $datamodel->tShowName];
            }
        }

        if ($model->load(Yii::$app->request->post()) ) {  
        
            $errors = false;
            foreach ($model->multiple as $multiple) {
            
            $model = new Sales();
            $model->load(Yii::$app->request->post());
            
            $model->sDate = date('d.m.Y');
            
            $model->sDate = $multiple['date'];
            $model->sStore = $multiple['store'];
            $model->sQty =  $multiple['qty'];
            $model->sPrice =  $multiple['price'];
            $model->sCountry = $multiple['country'];
            
            $model->sSumm =$model->sQty * $model->sPrice;
            $model->sAddDate = date('d.m.Y');
            $model->sAddTime = date('h:i:s');

            
            if ($model->save() ) {
                
                $uid = 0;
                
                if($model->sGoodWhat==0){
                    $find = $model->hasOne(\common\models\Tracks::className(), ['tId' => 'sGood'])->one();
                    $uid= isset($find->tName)?$find->tUser:0;
                }
                if($model->sGoodWhat==1){
                    $find = $model->hasOne(\common\models\Albums::className(), ['aId' => 'sGood'])->one();
                    $uid = isset($find->aName)?$find->aUser:0;
                }
                if($model->sGoodWhat==2){
                    $find = $model->hasOne(\common\models\Books::className(), ['bId' => 'sGood'])->one();
                    $uid = isset($find->bName)?$find->bUser:0;
                } 
    
                if ($uid) {
                    $modelBalance = new \common\models\Balance();
                    $modelBalance->acUser = $uid;
                    $modelBalance->acTs = time(); 
                    $modelBalance->acDate = date("d.m.Y");
                    $modelBalance->acTime = date("h:i:s");
                    $modelBalance->acPrih = $model->sSumm;
                    $modelBalance->acRash = 0;            
                    $modelBalance->acDesc = 'Royalty';  
                            
                    $modelBalance->save(); 
                    
                    $modelSalesPayed = new \common\models\SalesPayed();
                    $modelSalesPayed->spid = $modelBalance->acId;
                    $modelSalesPayed->sId = $model->sId; 
                    $modelSalesPayed->sum = $model->sSumm;
       
                    $modelSalesPayed->save();    
                }
                

            }
            else 
                $errors = true;
                
            }
            
            if ($errors) 
                Yii::$app->session->setFlash('error', 'Ошибка при сохранении!');
            else
                Yii::$app->session->setFlash('success', 'Добавлено успешно!');

            if ($destination)
                return $this->redirect([$destination]);
            else
                return $this->redirect(['index', 'id' => $model->sId]);
            
                               
        } else {
            return $this->render('ajaxcreate', [
                'model' => $model,
                'data' => $data,
                'item' => $item,
            ]);
        }
    }

    protected function performAjaxValidation($model)
    {
        if (\Yii::$app->request->isAjax && $model->load(\Yii::$app->request->post())) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            echo json_encode(ActiveForm::validate($model));
            \Yii::$app->end();
        }
    }


    public function actionGroup($good = NULL, $item = NULL, $destination = NULL)
    {
        $model = new Sales();

        //$this->performAjaxValidation($model);
        
        $data = ArrayHelper::map(\common\models\Tracks::find()->all(),'tId','tShowName');
        
        if ($good && $item) {
            if ($good == 1) {
                $model->sGoodWhat = 1;
                $model->sGood = $item;
                //$data = ArrayHelper::map(\common\models\Albums::find()->all(),'aId','aName');
                $datamodel = \common\models\Albums::find()->where(['=','aId',$item])->one();
                if (isset($datamodel->aId))
                    $data = [$datamodel->aId => $datamodel->aName];
            } 
            elseif ($good == 2) {
                $model->sGoodWhat = 0;
                $model->sGood = $item;
                
                $datamodel = \common\models\Tracks::find()->where(['=','tId',$item])->one();
                if (isset($datamodel->tId))
                    $data = [$datamodel->tId => $datamodel->tShowName];
            }
        }

        if ($model->load(Yii::$app->request->post()) ) {  
        
            $errors = false;

            foreach ($model->sGood as $sGood) {
            foreach ($model->multiple as $multiple) {
            
            $model = new Sales();
            $model->load(Yii::$app->request->post());

            $model->sGood = $sGood;

            $model->sDate = date('d.m.Y');
            
            $model->sDate = $multiple['date'];
            $model->sStore = $multiple['store'];
            $model->sQty =  $multiple['qty'];
            $model->sPrice =  $multiple['price'];
            $model->sCountry = $multiple['country'];
            
            $model->sSumm =$model->sQty * $model->sPrice;
            $model->sAddDate = date('d.m.Y');
            $model->sAddTime = date('h:i:s');

            
            if ($model->save() ) {
                
                $uid = 0;
                
                if($model->sGoodWhat==0){
                    $find = $model->hasOne(\common\models\Tracks::className(), ['tId' => 'sGood'])->one();
                    $uid= isset($find->tName)?$find->tUser:0;
                }
                if($model->sGoodWhat==1){
                    $find = $model->hasOne(\common\models\Albums::className(), ['aId' => 'sGood'])->one();
                    $uid = isset($find->aName)?$find->aUser:0;
                }
                if($model->sGoodWhat==2){
                    $find = $model->hasOne(\common\models\Books::className(), ['bId' => 'sGood'])->one();
                    $uid = isset($find->bName)?$find->bUser:0;
                } 
    
                if ($uid) {
                    $modelBalance = new \common\models\Balance();
                    $modelBalance->acUser = $uid;
                    $modelBalance->acTs = time(); 
                    $modelBalance->acDate = date("d.m.Y");
                    $modelBalance->acTime = date("h:i:s");
                    $modelBalance->acPrih = $model->sSumm;
                    $modelBalance->acRash = 0;            
                    $modelBalance->acDesc = 'Royalty';  
                            
                    $modelBalance->save(); 
                    
                    $modelSalesPayed = new \common\models\SalesPayed();
                    $modelSalesPayed->spid = $modelBalance->acId;
                    $modelSalesPayed->sId = $model->sId; 
                    $modelSalesPayed->sum = $model->sSumm;
       
                    $modelSalesPayed->save();    
                }
                

            }
            else 
                $errors = true;
                
            }
            }

            if ($errors) 
                Yii::$app->session->setFlash('error', 'Ошибка при сохранении!');
            else
                Yii::$app->session->setFlash('success', 'Добавлено успешно!');

            if ($destination)
                return $this->redirect([$destination]);
            else
                return $this->redirect(['index', 'id' => $model->sId]);
            
                               
        } else {
            return $this->render('groupcreate', [
                'model' => $model,
                'data' => $data,
                'item' => $item,
            ]);
        }
    }

    /**
     * Updates an existing Sales model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->sGoodWhat == 0){
      $data = ArrayHelper::map(\common\models\Tracks::find()->all(),'tId','tShowName');
        }
        elseif($model->sGoodWhat == 1){
            $data = ArrayHelper::map(\common\models\Albums::find()->all(),'aId','aName'); 
        }
        elseif($model->sGoodWhat == 2 ){
            $data = ArrayHelper::map(\common\models\Books::find()->all(),'bId','name');
        }
        else 
            $data = NULL;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->sId]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'data' => $data
            ]);
        }
    }

    /**
     * Deletes an existing Sales model.
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
     * Finds the Sales model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Sales the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Sales::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionData($id = 0)
    {
        if($id==0){
      $data = ArrayHelper::map(\common\models\Tracks::find()->all(),'tId','tShowName');
        }
        if($id==1){
            $data = ArrayHelper::map(\common\models\Albums::find()->all(),'aId','aName'); 
        }
        if($id==2){
            $data = ArrayHelper::map(\common\models\Books::find()->all(),'bId','name');
        }     
        
        foreach($data as $key => $opt){
                echo "<option value='".$key."'>".$opt."</option>";
        }
    }
    
    public function actionUnpublished()
    {

        $query = \common\models\Albums::find()
            // ->joinWith(['published'])
            //  ->select(['t2a.aId', 't2a.tId', 'published.aId as paId'])
            ->where(['<','(SELECT COUNT(*) FROM published p WHERE albums.aId = p.aId)',1])
            // ->groupBy('albums.aId')
        ;
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['aId'=>SORT_DESC]]
        ]);

        return $this->render('unpublished', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPublished()
    {
    //'SELECT * FROM s2u WHERE `s2uService` = 6 AND (s2uStatus = 3 OR (s2uStatus in ('completed','archive')))ORDER BY s2uId DESC
/*        $query = \common\models\S2u::find()
            ->where('s2uService = 6 AND s2uStatus = 3 ')
        ;
        $searchModel = new \common\models\S2uSearch();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['s2uId'=>SORT_DESC]]
        ]);

*/
        $searchModel = new \common\models\S2uSearch();
        
        $params = Yii::$app->request->queryParams;
        $params['S2uSearch']['s2uStatus'] = 3;
        $params['S2uSearch']['s2uService'] = 6;
        $dataProvider = $searchModel->search($params);


        return $this->render('published', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }


    public function actionLabels()
    {
        $searchModel = new \common\models\LabelsSearch();

        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);
        
        return $this->render('labels', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCsv()
    {

        if (Yii::$app->request->post() ) {
             
        $file = UploadedFile::getInstanceByName('attachment');
        if ($file && $file->tempName) {
            $newname=Yii::getAlias('csv/')."import.csv";
            if (move_uploaded_file($file->tempName, "$newname")) {
                
            }
   
        $csv=false;
        $csv=file(Yii::getAlias('csv/')."import.csv");
        if (!$csv) {echo "Ошибка чтения CSV файла"; die();}
        if (count($csv)<1) {echo "CSV файл пуст"; die();}
                    
        $errors=false;
    foreach ($csv as $id=>$str){
      if ($id==0) continue;
      if ($str){
      $cols=explode(';',$str);
      $saleDate  =$cols[0];
      $Album    =$cols[1];
      $Track    =$cols[2];
      $Price    =$cols[3];
      $Qty    =$cols[4];
      $Summ    =$cols[5];
      $Type    =$cols[6];
      $Store    =$cols[7];
      $Country  =$cols[8];
      
      if (!$saleDate) $errors.= "Строка #".($id+1).": Не указана дата продажи<br>";
      if (!$Album) {
        $errors.= "Строка #".($id+1).": Не указан альбом<br>";
         
      } else {
                if (!isset(\common\models\Albums::find()->where(['=','aName', $Album])->one()->aName)) {
        $errors.= "Строка #".($id+1).": Альбом <i>$Album</i> не найден<br>";
        }
      }
      if($Type=='0'){
      if (!$Track) {
        $errors.= "Строка #".($id+1).": Не указан трек, а тип установлен как трек.<br>";
      } else {
        if(!isset(\common\models\Tracks::find()->where(['=','tName', $Track])->one()->tName)){
        $errors.= "Строка #".($id+1).": Трек <i>$Track</i> не найден<br>";
        }
      } 
      }
      if (!$Price)   $errors.= "Строка #".($id+1).": Не указана цена<br>";
      if (!$Qty)     $errors.= "Строка #".($id+1).": Не указано кол-во<br>";
      if (!$Summ)   $errors.= "Строка #".($id+1).": Не указана сумма<br>";
      if ($Type!='0'&& $Type!='1') $errors.= "Строка #".($id+1).": Неверно указан тип<br>";
      if (!$Store) {
        $errors.= "Строка #".($id+1).": Не указан альбом<br>";
      } else {
                if(!isset(\common\models\Stores::find()->where(['=','sName', $Store])->one()->sName)) {
        $errors.= "Строка #".($id+1).": Магазин <i>$Store</i> не найден<br>";
        }
      }
      if (!$Country)   $errors.= "Строка #".($id+1).": Не указана страна<br>";
      }
    }
    
    if ($errors){
            echo "<b>Файл не импортирован. Найдены ошибки:<br><br></b>".$errors;
            die();
    }
        
        $rows= 0;
        foreach ($csv as $id=>$str){
      if ($id==0) continue;
      if ($str){
      $cols=explode(';',$str);
      $saleDate  =$cols[0];
      $Album    =$cols[1];
      $Track    =$cols[2];
      $Price    =$cols[3];
      $Qty    =$cols[4];
      $Summ    =$cols[5];
      $Type    =$cols[6];
      $Store    =$cols[7];
      $Country  =$cols[8];
            $Albums = \common\models\Albums::find()->where(['=','aName', $Album])->one();
            $Tracks= \common\models\Tracks::find()->where(['=','tName', $Track])->one();
            $Stores = \common\models\Stores::find()->where(['=','sName', $Store])->one();
            

      if ($Type=='0') $good=$Tracks->tId;
      if ($Type=='1') $good=$Albums->aId;
            
            $model = new \common\models\Sales();
            $model->sDate = $saleDate;
            $model->sQty = $Qty; 
            $model->sPrice = $Price;
            $model->sSumm = $Summ;                     
            $model->sGood = $good;
            $model->sGoodWhat = $Type; 
            $model->sStore = ''.$Stores->sId;
            $model->sCountry = $Country;             
            $model->sAddDate = date('d.m.Y');
            $model->sAddTime = date('H:i:s');             

                            
            if ($model->save()) {
                
            }
            else {
                var_dump($model);
                die;
            }
                
                    
            $rows++;
      }
    }
        Yii::$app->session->setFlash('success', Yii::t('app', 'Импорт завершен! '.$rows.' строк'));
        }     


        }
        
        return $this->render('csv', [
         
        ]);
    }

}
