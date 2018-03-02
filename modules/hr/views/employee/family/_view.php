<?php

use yii\web\View;
use yii\grid\GridView;

use yii\data\ArrayDataProvider;

use app\modules\hr\models\embedded\Family;

/* @var $this View */
/* @var $models Family[]|array */

?>

<?= GridView::widget([
    'dataProvider' => new ArrayDataProvider([
        'allModels' => $models,
        'modelClass' => Family::className(),
    ]),
    'summary' => false,
    'emptyText' => false,
    'columns' => [
        [
            'attribute' => '__kinship',
            'value' => function($model) {
                /** @var Family $model */
                return $model->getKinship();
            }
        ],
        'full_name',
        'birth_date',
        'note',
    ],
]) ?>
