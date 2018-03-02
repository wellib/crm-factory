<?php

use app\modules\accounts\models\User;
use app\modules\canteen\models\OrderSearch;
use app\modules\canteen\models\ReportForm;
use kartik\daterange\DateRangePicker;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $orderSearch OrderSearch */
/* @var $userList User[] */
?>

<?php $form = ActiveForm::begin([
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
    ]
]); ?>

<?= $form->field($orderSearch, 'employee_ids')->widget(Select2::className(), [
    'theme' => Select2::THEME_DEFAULT,
    'data' => ArrayHelper::map($userList, 'id', 'name'),
    'options' => [
        'placeholder' => 'Выберите сотрудника ...',
        'multiple' => true,
    ],
    'pluginOptions' => [
        'allowClear' => true
    ],
]) ?>

<div class="form-group">
    <?= Html::submitButton('Сформировать отчет', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>
