<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\hr\Module;

/* @var $this yii\web\View */
/* @var $model app\modules\hr\models\DictionaryWord */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="dictionary-word-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, '_id') ?>

    <?= $form->field($model, 'dictionary') ?>

    <?= $form->field($model, 'word') ?>

    <?= $form->field($model, 'created_at') ?>

    <?= $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Module::t('dictionary-word', 'Create') : Module::t('dictionary-word', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
