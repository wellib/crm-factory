<?php

use yii\web\View;

use yii\widgets\ActiveForm;

use app\modules\hr\models\Order;
use app\modules\hr\models\embedded\Fired;
use app\modules\hr\models\DictionaryWord;
use app\modules\hr\widgets\DictionaryWordInputWidget;

use kartik\date\DatePicker;


/* @var $this View */
/* @var $owner Order */
/* @var $model Fired */
/* @var $form ActiveForm */

$datePickerConfig = [
    'pickerButton' => false,
    'pluginOptions' => [
        'autoclose' => true,
        'format' => Fired::DATE_REGEXP_PATTERN_FOR_DATE_PICKER,
    ],
];
?>

<?= $form->field($model, 'date')->widget(DatePicker::className(), $datePickerConfig) ?>

<?= $form->field($model, 'based_on_documents')->textarea() ?>

<?= $form->field($model, '__base_dismissal')->widget(DictionaryWordInputWidget::className(), [
    'dictionary' => DictionaryWord::DICTIONARY_BASE_DISMISSAL,
]) ?>


