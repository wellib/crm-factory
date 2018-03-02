<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\docs\models\ContractLog;

/* @var $this yii\web\View */
/* @var $model TaskLog */
/* @var $task_id mixed */
/* @var $form yii\widgets\ActiveForm */

$action = isset($action) ? $action : ['send-comment', 'id' => $contract_id];

?>

<?php $form = ActiveForm::begin([
    'action' => $action,
    'options' => [
        'data-pjax' => true,
        'enctype' => 'multipart/form-data',
    ],
]) ?>

    <?= $form->field($model, 'comment')->textarea() ?>
    <?= $form->field($model, 'attachedFilesUpload[]')->fileInput(['multiple' => true]) ?>
    <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end() ?>
