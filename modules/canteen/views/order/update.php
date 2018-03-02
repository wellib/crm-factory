<?php

use app\modules\accounts\models\User;
use app\modules\canteen\models\OrderForm;
use app\themes\gentelella\widgets\Panel;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $orderForm OrderForm */
/* @var $userList User[] */

$this->title = 'Редактирование заказа';
$this->params['breadcrumbs'][] = [
    'label' => 'Заказы',
    'url' => ['index'],
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="canteen-order-update">

    <?php Panel::begin(); ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'orderForm' => $orderForm,
        'userList' => $userList,
    ]) ?>

    <?php Panel::end(); ?>

</div>
