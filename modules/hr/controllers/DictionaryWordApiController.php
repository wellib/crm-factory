<?php

namespace app\modules\hr\controllers;

use Yii;

use yii\helpers\ArrayHelper;

use yii\filters\AccessControl;

use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;

use app\modules\hr\Module;
use app\modules\hr\models\DictionaryWord;

/**
 * Class DictionaryWordApiController
 * @package app\modules\hr\controllers
 */
class DictionaryWordApiController extends ActiveController
{

    public $modelClass = 'app\modules\hr\models\DictionaryWord';
    public $createScenario = DictionaryWord::SCENARIO_CREATE;
    public $updateScenario = DictionaryWord::SCENARIO_UPDATE;


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

        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }


    public function prepareDataProvider()
    {
        $query = DictionaryWord::find();
        if (($dictionary = Yii::$app->getRequest()->get('dictionary', null))) {
            $query->dictionary(intval($dictionary));
        }
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
    }
}
