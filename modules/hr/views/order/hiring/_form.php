<?php

use yii\web\View;

use yii\widgets\ActiveForm;

use app\modules\hr\models\Order;
use app\modules\hr\models\embedded\Hiring;
use app\modules\hr\models\DictionaryWord;
use app\modules\hr\widgets\DictionaryWordInputWidget;

use kartik\date\DatePicker;


/* @var $this View */
/* @var $owner Order */
/* @var $model Hiring */
/* @var $form ActiveForm */

$datePickerConfig = [
    'pickerButton' => false,
    'pluginOptions' => [
        'autoclose' => true,
        'format' => Hiring::DATE_REGEXP_PATTERN_FOR_DATE_PICKER,
    ],
];
?>

<?= $form->field($model, 'probation_months')->textInput() ?>

<?= $form->field($model, 'employment')->dropDownList(Hiring::employmentLabels(), ['prompt' => '']) ?>

<?= $form->field($model, '__employment_term')->widget(DictionaryWordInputWidget::className(), [
    'dictionary' => DictionaryWord::DICTIONARY_EMPLOYMENT_TERM,
]) ?>

<?= $form->field($model, 'date_begin')->widget(DatePicker::className(), $datePickerConfig) ?>

<?= $form->field($model, 'date_end')->widget(DatePicker::className(), $datePickerConfig) ?>

