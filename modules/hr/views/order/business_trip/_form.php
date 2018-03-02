<?php

use yii\web\View;

use yii\widgets\ActiveForm;

use app\modules\hr\models\Order;
use app\modules\hr\models\embedded\BusinessTrip;

use kartik\date\DatePicker;


/* @var $this View */
/* @var $owner Order */
/* @var $model BusinessTrip */
/* @var $form ActiveForm */

$datePickerConfig = [
    'pickerButton' => false,
    'pluginOptions' => [
        'autoclose' => true,
        'format' => BusinessTrip::DATE_REGEXP_PATTERN_FOR_DATE_PICKER,
    ],
];
?>

<?= $form->field($model, 'duration_in_days')->textInput() ?>

<?= $form->field($model, 'date_begin')->widget(DatePicker::className(), $datePickerConfig) ?>

<?= $form->field($model, 'date_end')->widget(DatePicker::className(), $datePickerConfig) ?>

<?= $form->field($model, 'location')->textInput() ?>

<?= $form->field($model, 'organization')->textInput() ?>

<?= $form->field($model, 'purpose')->textInput() ?>

<?= $form->field($model, 'based_on')->textInput() ?>




