<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\accounts\Module;

/* @var $this yii\web\View */
/* @var $model app\modules\accounts\models\backend\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, '_id') ?>

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'password_hash') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'avatar') ?>

    <?php // echo $form->field($model, 'position') ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('user', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Module::t('user', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
