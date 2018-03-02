<?php

use yii\web\View;

use yii\helpers\Html;

use yii\grid\GridView;

use yii\data\ArrayDataProvider;

use app\modules\hr\models\File;

/* @var $this View */
/* @var $models File[]|array */

?>

<?= GridView::widget([
    'dataProvider' => new ArrayDataProvider([
        'allModels' => $models,
        'modelClass' => File::className(),
    ]),
    'summary' => false,
    'emptyText' => false,
    'columns' => [
        [
            'attribute' => 'file',
            'format' => 'raw',
            'value' => function($model) {
                /** @var File $model */
                return Html::a($model->name, $model->getDownloadUrl(true), [
                    'target' => '_blank',
                ]);
            },
            'contentOptions' => [
                'style' => 'max-width: 10px;',
            ],
        ],
        'description',
    ],
]) ?>
