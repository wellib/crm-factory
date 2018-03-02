<?php

use yii\web\View;
use yii\grid\GridView;

use yii\data\ArrayDataProvider;

use app\modules\hr\models\embedded\Contact;

/* @var $this View */
/* @var $models Contact[]|array */

?>

<?= GridView::widget([
    'dataProvider' => new ArrayDataProvider([
        'allModels' => $models,
        'modelClass' => Contact::className(),
    ]),
    'summary' => false,
    'emptyText' => false,
    'columns' => [
        [
            'attribute' => '__type',
            'value' => function($model) {
                /** @var Contact $model */
                return $model->getType();
            }
        ],
        'value',
        'description',
        'main:boolean',
    ],
]) ?>
