<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\docs\Module;

/* @var $this yii\web\View */
/* @var $model app\modules\docs\models\CalendarPeriodSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="calendar-period-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, '_id') ?>

    <?= $form->field($model, 'from_date') ?>

    <?= $form->field($model, 'to_date') ?>

    <?= $form->field($model, 'every_year') ?>

    <?= $form->field($model, 'name') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('calendar_period', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Module::t('calendar_period', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
