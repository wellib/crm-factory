<?php

use app\modules\canteen\components\DaysWeek;
use app\modules\canteen\models\Dish;
use kartik\grid\GridView;
use yii\base\Model;
use yii\bootstrap\Html;
use yii\data\DataProviderInterface;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider DataProviderInterface */
/* @var $searchModel Model */

$this->title = 'Меню на неделю';
?>

<div class="canteen-dish-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить блюдо', ['create'], ['class' => 'btn btn-success']) ?>
        <?php if (Yii::$app->getModule('canteen')->canteen->isOpen()): ?>
            <?= Html::a('Закрыть столовую', ['default/close'], ['class' => 'btn btn-primary pull-right']) ?>
        <?php else: ?>
            <?= Html::a('Открыть столовую', ['default/open'], ['class' => 'btn btn-danger pull-right']) ?>
        <?php endif; ?>
        <?= Html::a('Форма для заказа', ['default/order-only'], ['class' => 'btn btn-primary pull-right']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <div class="table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'contentOptions' => [
                        'style' => 'width: 30px;',
                    ],
                ],
                [
                    'attribute' => 'week_day',
                    'filter' => DaysWeek::$days,
                    'value' => function (Dish $dish) {
                        return DaysWeek::getDayName($dish->week_day);
                    },
                    'contentOptions' => [
                        'style' => 'width: 150px;',
                    ],
                ],
                'name',

                [
                    'attribute' => 'price',
                    'format' => ['decimal', 2],
                    'contentOptions' => [
                        'style' => 'width: 150px;',
                    ],
                ],
                [
                    'attribute' => 'portion',
                    'format' => ['decimal', 1],
                    'contentOptions' => [
                        'style' => 'width: 150px;',
                    ],
                ],
                [
                    'class' => \app\themes\gentelella\widgets\grid\ActionColumn::className(),
                    'template' => '{update} {delete}',
                    'contentOptions' => [
                        'style' => 'width: 115px;',
                    ],
                ],
            ],
        ]); ?>

    </div>

    <?php Pjax::end(); ?>

</div>