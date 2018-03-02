<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use app\modules\docs\Module;
use kartik\datetime\DateTimePicker;
/* @var $this yii\web\View */
/* @var $model app\modules\docs\models\Calendar */
/* @var $form yii\widgets\ActiveForm */

$calendar_event_types = array (
   'alert' => 'Предупреждение',
);

$dateTimePickerConfig = [
    'options' => ['placeholder' => 'Дата и время'],
    'pluginOptions' => [
        'autoclose' => true,
        'format' => 'dd.mm.yyyy hh:ii',
        'daysOfWeekDisabled' => [0,6],
        'todayHighlight' => true,
        'minuteStep' => 60,
        'minView' => 1,
        'startDate' => date('Y-m-d'), 
        ]
    ];
?>

<div class="calendar-form">

    <?php $form = ActiveForm::begin(); ?>

      <?= $form->field($model, 'name') ?>


      <label class="control-label" for="calendar-name">Время проведения</label>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'date')->widget(DateTimePicker::classname(), $dateTimePickerConfig)->label(false) ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'date_to')->widget(DateTimePicker::classname(), $dateTimePickerConfig)->label(false) ?>
        </div>
      </div>

      <?//= $form->field($model, 'place') ?>

      <?//= $form->field($model, 'type')->dropDownList($calendar_event_types) ?>

      <?= $form->field($model, 'description')->textArea(['rows' => '6'])?>

      <?= $form->field($model, 'type')->dropDownList(['work' => 'Рабочий', 'personal' => 'Личный']) ?>

      <?= $form->field($model, 'notify')->dropDownList(['за 15 минут','за 30 минут','за 1 час',]) ?>

        <?php
        if (!$model->isNewRecord) {
           echo $form->field($model, 'status')->dropDownList([0 => 'В процессе', 1 => 'Выполнено']);
        }
        ?>


      <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php
        if (!$model->isNewRecord) {
          $options = array_merge([
                            'title' => Yii::t('yii', 'Удалить'),
                            'aria-label' => Yii::t('yii', 'Удалить'),
                            'data-pjax' => '0',
                            'class' => 'btn btn-danger',
                            'data' => [
                              'confirm' => 'Вы уверены, что хотите удалить?',
                              'method' => 'post',
                             ],              
                        ], []);
            echo Html::a('Удалить', ['delete', 'id' => $model->getId()], $options);
        }
        ?>
      </div>
    <?php ActiveForm::end(); ?>
</div>
