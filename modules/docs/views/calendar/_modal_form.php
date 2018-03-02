<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use kartik\datetime\DateTimePicker;
use yii\bootstrap\Modal;

$calendar_event_types = array (
   'alert' => 'Предупреждение',
);

$dateTimePickerConfig = [
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

$params = Yii::$app->request->queryParams;
if (isset($params['CalendarSearch']['type']))
    $model->type = $params['CalendarSearch']['type'];

?>

<?php Modal::begin([
    'id' => 'myModal',
    'header' => '',
    'clientOptions' => [
        'keyboard' => false,
        'backdrop' => 'static',
    ],
]) ?>


      <?php $form = ActiveForm::begin(); ?>
      <div class="btn-body">
<h3>Добавить событие</h3>
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


     </div>
      <div class="btn-footer">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Отменить</button>
      </div>
      <?php ActiveForm::end(); ?>

<?php Modal::end() ?>

