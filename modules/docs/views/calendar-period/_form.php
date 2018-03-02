<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\docs\Module;
use app\modules\docs\models\CalendarPeriod;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model CalendarPeriod */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="calendar-period-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-2">
            <?= $form->field($model, 'fromDateFormat')->widget(DatePicker::className(),[
                'attribute' => 'fromDateFormat',
                'attribute2' => 'toDateFormat',
                'pickerButton' => false,
                //'language' => 'ru',
                'type' => DatePicker::TYPE_RANGE,
                'separator' => '&#8594;',
                'pluginOptions' => [
                    //'language' => 'ru',

                    'autoclose' => true,
                    'format' => 'dd.mm.yyyy',
                    //'daysOfWeekDisabled' => [0,6],
                    'todayHighlight' => true,
                    'todayBtn' => false,
                ]
            ]) ?>
        </div>
    </div>




    <? //= $form->field($model, 'every_year')->checkbox() ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'type')->dropDownList($model->getTypeList()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Module::t('calendar_period', 'CREATE__FORM__SUBMIT_BTN') : Module::t('calendar_period', 'UPDATE__FORM__SUBMIT_BTN'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
