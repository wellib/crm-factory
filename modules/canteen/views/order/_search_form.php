<?php

use app\modules\accounts\models\User;
use app\modules\canteen\models\OrderSearch;
use kartik\daterange\DateRangePicker;
use kartik\form\ActiveForm;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $orderSearch OrderSearch */
/* @var $userList User[] */
?>

<?php $form = ActiveForm::begin([
    'id' => 'date-range-form',
    'successCssClass' => '',
    'method' => 'get',
]); ?>

<?= $form->field($orderSearch, 'created_at_range', [
    'addon' => ['prepend' => ['content' => '<i class="glyphicon glyphicon-calendar"></i>']],
    'options' => ['class' => 'drp-container form-group']
])->widget(DateRangePicker::classname(), [
    'presetDropdown' => true,
    'startAttribute' => 'created_at_from',
    'endAttribute' => 'created_at_to',
    'pluginOptions' => [
        'showDropdowns' => true,
        'alwaysShowCalendars' => true,
        'locale' => [
            'format' => 'DD.MM.YYYY',
        ],
    ],
    'pluginEvents' => [
        'apply.daterangepicker' => new JsExpression("function() { 
            $('#date-range-form').submit();
         }"),
    ],
]); ?>

<?php ActiveForm::end(); ?>
