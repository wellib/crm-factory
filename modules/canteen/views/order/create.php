<?php

use app\modules\accounts\models\User;
use app\modules\canteen\models\Dish;
use app\modules\canteen\models\OrderForm;
use app\themes\gentelella\widgets\Panel;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $orderForm OrderForm */
/* @var $userList User[] */

$this->title = 'Новый заказ';
$this->params['breadcrumbs'][] = [
    'label' => 'Заказы',
    'url' => ['my'],
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="canteen-order-create">

    <?php Panel::begin(); ?>

    <h1><?= Html::encode($this->title) ?><small> (Сегодня – <?= Yii::$app->formatter->asDate(time(), 'EEEE, d MMMM')?>)</small></h1>

    <?= $this->render('_form', [
        'orderForm' => $orderForm,
        'userList' => $userList,
    ]) ?>

    <?php Panel::end(); ?>

</div>