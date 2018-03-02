<?php

use yii\web\View;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

use app\modules\hr\Module;
use app\modules\hr\models\embedded\IdentityCard;
use app\modules\hr\models\DictionaryWord;
use app\modules\hr\widgets\DictionaryWordInputWidget;

use kartik\date\DatePicker;


/* @var $this View */
/* @var $model IdentityCard */
/* @var $form ActiveForm */
?>

<?= $form->field($model, 'id_number') ?>

<?= $form->field($model, 'vat_id') ?>

<?= $form->field($model, 'issue_date')->widget(DatePicker::className(), [
    'pickerButton' => false,
    'pluginOptions' => [
        'autoclose' => true,
        'format' => 'dd.mm.yyyy',
    ],
]) ?>

<?= $form->field($model, '_issuing_authority')->widget(DictionaryWordInputWidget::className(), [
    'dictionary' => DictionaryWord::DICTIONARY_ISSUING_AUTHORITY,
]) ?>

<?= $form->field($model, '_nationality')->widget(DictionaryWordInputWidget::className(), [
    'dictionary' => DictionaryWord::DICTIONARY_NATIONALITY,
]) ?>

<?= $form->field($model, 'birthplace')->textarea() ?>

<?= $form->field($model, 'registration_address')->textarea() ?>

<?= $form->field($model, 'residential_address')->textarea() ?>

<?= $form->field($model, '_marital_status')->widget(DictionaryWordInputWidget::className(), [
    'dictionary' => DictionaryWord::DICTIONARY_MARITAL_STATUS,
]) ?>



