<?php

use yii\web\View;
use yii\grid\GridView;

use yii\data\ArrayDataProvider;

use app\modules\hr\models\embedded\Experience;

/* @var $this View */
/* @var $models Experience[]|array */

?>

<?= GridView::widget([
    'dataProvider' => new ArrayDataProvider([
        'allModels' => $models,
        'modelClass' => Experience::className(),
    ]),
    'summary' => false,
    'emptyText' => false,
    'columns' => [
        'start_date',
        'end_date',
        'organization',
        'position',
        [
            'attribute' => '__dismissal_reason',
            'value' => function($model) {
                /** @var Experience $model */
                return $model->getDismissalReason();
            }
        ],
    ],
]) ?>
