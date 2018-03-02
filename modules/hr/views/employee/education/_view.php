<?php

use yii\web\View;
use yii\grid\GridView;

use yii\data\ArrayDataProvider;

use app\modules\hr\models\embedded\Education;

/* @var $this View */
/* @var $models Education[]|array */

?>

<?= GridView::widget([
    'dataProvider' => new ArrayDataProvider([
        'allModels' => $models,
        'modelClass' => Education::className(),
    ]),
    'summary' => false,
    'emptyText' => false,
    'columns' => [
        'institution',
        'graduation_year',
        'certificated',
        'degree',
        'specialty',
    ],
]) ?>
