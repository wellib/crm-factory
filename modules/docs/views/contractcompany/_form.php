<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\docs\models\Contractcompany */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contractcompany-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
