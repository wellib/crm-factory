<?php

namespace app\modules\hr\controllers;


use Yii;

use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;

use yii\filters\AccessControl;

use yii\web\NotFoundHttpException;

use app\modules\hr\Module;
use app\modules\hr\models\File;

/**
 * Class DictionaryWordApiController
 * @package app\modules\hr\controllers
 */
class FileApiController extends ActiveController
{
    public $modelClass = 'app\modules\hr\models\File';
    public $createScenario = File::SCENARIO_CREATE;
    public $updateScenario = File::SCENARIO_UPDATE;

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [Module::ROLE_NAME],
                    ],
                ],
            ],
        ]);
    }
    
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionDownload($id)
    {
        $model = $this->findModel($id);
        return Yii::$app->getResponse()->sendFile($model->getFilePath(), $model->name);
        /**
         * Лучше конено сделать с помощью xSendFile, как показано ниже
         * Yii::$app->getResponse()->xSendFile($model->getFilePath(), $model->name);
         * Но в данном случае придется еще вносить правки в конфиг сервера, а это не универсально, к сожелению :(
         */
    }

    /**
     * Finds the Employee model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return File the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = File::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(
                Module::t('file', 'HTTP_ERROR__NOT_FOUND')
            );
        }
    }
}
