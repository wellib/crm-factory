<?php

use yii\web\View;
use yii\widgets\ActiveForm;

use app\modules\hr\models\embedded\CompanyCard;

use app\modules\structure\widgets\DepartmentInputWidget;

use kartik\date\DatePicker;


/* @var $this View */
/* @var $model CompanyCard */
/* @var $form ActiveForm */
?>

<?= $form->field($model, 'employee_id') ?>

<?= $form->field($model, 'biometrics_id') ?>

<?= $form->field($model, 'contract_number') ?>

<?= $form->field($model, 'contract_date')->widget(DatePicker::className(), [
    'pickerButton' => false,
    'pluginOptions' => [
        'autoclose' => true,
        'format' => 'dd.mm.yyyy',
    ],
]) ?>

<?= $form->field($model, 'employment_date')->widget(DatePicker::className(), [
    'pickerButton' => false,
    'pluginOptions' => [
        'autoclose' => true,
        'format' => 'dd.mm.yyyy',
    ],
]) ?>

<?= $form->field($model, '_structure_department')->widget(DepartmentInputWidget::className()) ?>


