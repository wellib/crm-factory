<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\hr\Module;


/* @var $this yii\web\View */
/* @var $model app\modules\hr\models\EmployeeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employee-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, '_id') ?>

    <?= $form->field($model, 'created_at') ?>

    <?= $form->field($model, 'updated_at') ?>

    <?= $form->field($model, 'first_name') ?>

    <?= $form->field($model, 'middle_name') ?>

    <?php // echo $form->field($model, 'last_name') ?>

    <?php // echo $form->field($model, 'sex') ?>

    <?php // echo $form->field($model, 'birthday') ?>

    <?php // echo $form->field($model, '_user') ?>

    <?php // echo $form->field($model, '_identity_card') ?>

    <?php // echo $form->field($model, '_enterprise') ?>

    <?php // echo $form->field($model, '_contacts') ?>

    <?php // echo $form->field($model, '_education') ?>

    <?php // echo $form->field($model, '_family') ?>

    <?php // echo $form->field($model, '_experience') ?>

    <?php // echo $form->field($model, '_files') ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('employee', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Module::t('employee', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
