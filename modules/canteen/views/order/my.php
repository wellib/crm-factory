<?php

use app\modules\canteen\models\Order;
use app\modules\canteen\models\OrderSearch;
use kartik\grid\GridView;
use kartik\grid\SerialColumn;
use yii\base\Model;
use yii\bootstrap\Html;
use yii\data\DataProviderInterface;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider DataProviderInterface */
/* @var $orderSearch OrderSearch */

$this->title = 'Мои заказы';

$columns = [
    [
        'class' => SerialColumn::className(),
        'contentOptions' => [
            'style' => 'width: 30px;',
        ],
    ],
    [
        'label' => 'Дата',
        'format' => 'date',
        'value' => function ($model) {
            return ArrayHelper::getValue($model, 'created_at');
        },
        'contentOptions' => [
            'style' => 'width: 100px;',
        ],
        'pageSummary' => function () {
            return 'Всего';
        },
        'pageSummaryOptions' => [
            'class' => 'text-right',
        ],
    ],
    [
        'label' => 'Сумма заказа',
        'format' => ['decimal', 2],
        'value' => function ($model) {
            $price = 0;
            foreach ($model['orderDishList'] as $orderDish) {
                $price += $orderDish['quantity'] * $orderDish['price'];
            }

            return $price;
        },
        'contentOptions' => [
            'style' => 'width: 50px; text-align:right;',
        ],
        'pageSummary' => true,
        'pageSummaryOptions' => [
            'class' => 'text-right',
        ],
        'pageSummaryFunc' => function ($data) {
            $amount = 0;
            foreach ($data as $item) {
                $amount += $item;
            }

            return $amount;
        }
    ],
    [
        'label' => 'Состав заказа',
        'format' => 'raw',
        'value' => function ($model) {

            $tableHeder = Html::tag('tr', Html::tag('th', 'Блюдо', ['style' => 'width: 100px;']) . Html::tag('th', 'Порция', ['style' => 'width: 100px;']) . Html::tag('th', 'Цена', ['style' => 'width: 100px;']));
            $tableBody = '';
            $tableTdList = ArrayHelper::getColumn($model['orderDishList'], function ($data) {
                return Html::tag('td', $data['name']) . Html::tag('td', $data['quantity'] . ' шт.' . ' x ' . $data['portion']) . Html::tag('td', Yii::$app->formatter->asDecimal($data['price'], 2));
            });
            foreach ($tableTdList as $tableTd) {
                $tableBody .= Html::tag('tr', $tableTd);
            }
            $table = Html::tag('table', $tableHeder . $tableBody);

            return $table;
        }
    ],
];
?>

<div class="canteen-dish-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_search_form', [
        'orderSearch' => $orderSearch,
    ]) ?>

    <p>
        <?= Html::a('Сделать заказ', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="table-responsive">

        <?= GridView::widget([
            'resizableColumns' => false,
            'dataProvider' => $dataProvider,
            'columns' => $columns,
            'showPageSummary' => true,
        ]); ?>

    </div>


</div>