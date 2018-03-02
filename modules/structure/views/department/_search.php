<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\structure\models\DepartmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="department-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, '_id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'icon') ?>

    <?= $form->field($model, '_parent') ?>

    <?= $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'update_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('department', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('department', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
