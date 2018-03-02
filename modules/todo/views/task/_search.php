<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\todo\Module;

/* @var $this yii\web\View */
/* @var $model app\modules\todo\models\TaskSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-search">

    <?php $form = ActiveForm::begin([
        //'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, '_id') ?>

    <?= $form->field($model, 'subject') ?>

    <?= $form->field($model, 'deadline_timestamp') ?>

    <?= $form->field($model, 'priority') ?>

    <?= $form->field($model, 'description') ?>

    <?php // echo $form->field($model, '_attached_files') ?>

    <?php // echo $form->field($model, '_author') ?>

    <?php // echo $form->field($model, '_users_performers') ?>

    <?php // echo $form->field($model, '_users_control_execution') ?>

    <?php // echo $form->field($model, '_users_control_results') ?>

    <?php // echo $form->field($model, '_users_notify_after_finishing') ?>

    <?php // echo $form->field($model, '_users_approve') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('task', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Module::t('task', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
