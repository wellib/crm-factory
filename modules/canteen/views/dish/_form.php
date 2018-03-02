<?php

use app\modules\canteen\components\DaysWeek;
use app\modules\canteen\models\DishForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $dishForm DishForm */
/* @var $form ActiveForm */
?>

<div class="task-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($dishForm, 'name')->textInput() ?>

    <?= $form->field($dishForm, 'week_day')->dropDownList(DaysWeek::$days) ?>

    <?= $form->field($dishForm, 'price')->textInput() ?>

    <?= $form->field($dishForm, 'portion')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', [
            'class' => 'btn btn-success',
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
