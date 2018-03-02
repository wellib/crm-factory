<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

use app\modules\hr\Module;
use app\modules\hr\models\Employee;

use kartik\date\DatePicker;
use kartik\select2\Select2;



/* @var $this View */
/* @var $model Employee */
/* @var $form ActiveForm */
?>


<?= $form->field($model, 'last_name') ?>

<?= $form->field($model, 'first_name') ?>

<?= $form->field($model, 'middle_name') ?>

<?= $form->field($model, 'sex')->dropDownList($model->getSexList(),[
    'prompt' => '',
]) ?>

<?= $form->field($model, 'birthday')->widget(DatePicker::className(), [
    'pickerButton' => false,
    'pluginOptions' => [
        'autoclose' => true,
        'format' => 'dd.mm.yyyy',
    ],
]) ?>

<?= $form->field($model, '_user')->widget(Select2::className(), [
    'theme' => Select2::THEME_DEFAULT,
    'data' => $model->getUsers(),

    'options' => [
        'multiple' => false,
        'prompt' => '',
    ],
    'showToggleAll' => false,
    'pluginOptions' => [
        'allowClear' => true,
        'minimumInputLength' => 0,
    ],
]) ?>

<?= $form->field($model, 'position') ?>

