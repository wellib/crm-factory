<?php

use app\modules\accounts\models\User;
use app\modules\canteen\assets\OrderAsset;
use app\modules\canteen\models\OrderForm;
use app\modules\canteen\Module;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;

/* @var $this View */
/* @var $orderForm OrderForm */
/* @var $form ActiveForm */
/* @var $userList User[] */

OrderAsset::register($this);
?>

<?php $form = ActiveForm::begin(); ?>

<?php if (Yii::$app->user->can(Module::ROLE_CANTEEN_ADMIN)): ?>

    <?= $form->field($orderForm, 'employee_id')->widget(Select2::className(), [
        'theme' => Select2::THEME_DEFAULT,
        'data' => ArrayHelper::map($userList, 'id', 'name'),
        'options' => [
            'placeholder' => 'Выберите сотрудника ...',
        ],
    ]) ?>

<?php endif; ?>

<label class="control-label">Меню на сегодня</label>

<?php foreach ($orderForm->orderDishes as $key => $dish): ?>

    <div id="dish-list" class="form-group">
        <div class="row">

            <?= Html::activeHiddenInput($dish, "[$key]dish_id") ?>

            <div class="col-md-1"><?= Html::activeDropDownList($dish, "[$key]quantity", array_combine(range(1, 10), range(1, 10)), [
                    'class' => 'form-control',
                ]) ?></div>
            <div class="col-md-11"><?= Html::activeCheckbox($dish, "[$key]in_order", [
                    'label' => $dish->name . ' (Порция ' . $dish->portion . ') - ' . $dish->price . ' тенге',
                ]) ?></div>

        </div>
    </div>

<?php endforeach; ?>

<div class="form-group">
    <?= Html::submitButton('Сделать заказ', [
        'class' => 'btn btn-success'
    ]) ?>
</div>

<?php ActiveForm::end(); ?>
