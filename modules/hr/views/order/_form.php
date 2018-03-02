<?php

use yii\web\View;
use yii\web\JsExpression;

use yii\helpers\Url;
use yii\helpers\Html;

use yii\bootstrap\ActiveForm;

use app\modules\hr\Module;
use app\modules\hr\models\Order;

use kartik\date\DatePicker;
use kartik\select2\Select2;


/* @var $this View */
/* @var $model Order */
/* @var $form ActiveForm */
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'number') ?>

    <?= $form->field($model, 'date')->widget(DatePicker::className(), [
        'pickerButton' => false,
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'dd.mm.yyyy',
        ],
    ]) ?>


    <?php $resultsPerRequest = \app\modules\hr\controllers\EmployeeController::SELECT2__RESULTS_PER_REQUEST; ?>
    <?= $form->field($model, '_employees')->widget(Select2::className(), [
        'theme' => Select2::THEME_DEFAULT,
        'initValueText' => array_map(function($model) {
            /** @var \app\modules\hr\models\Employee $model */
            return $model->getFullName();
        }, $model->employees ), // set the initial display text
        'options' => [
            'placeholder' => 'Search for a city ...',
            'multiple' => true,
            'prompt' => '',
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 0,
            'language' => 'ru',
            'ajax' => [
                'url' => Url::to(['/hr/employee/select2']),
                'dataType' => 'json',
                'delay' => 250,
                'data' => new JsExpression('function(params) { return { term:params.term, page: params.page}; }'),
                'processResults' => new JsExpression(<<< JS
                    function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.items,
                            pagination: {
                                more: (params.page * {$resultsPerRequest}) < data.total_count
                            }
                        };
                    }
JS
                ),
                'cache' => true

            ],
            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'templateResult' => new JsExpression('function(employee) { return employee.text; }'),
            'templateSelection' => new JsExpression('function (employee) { return employee.text; }'),
        ],
    ]) ?>


    <?= $this->render($model->getEmbeddedModelViewDir() . '/_form', [
        'form'  => $form,
        'owner' => $model,
        'model' => $model->getEmbeddedModelByType(),
    ]) ?>


    <?= $form->field($model, 'note')->textarea() ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord
            ? Module::t('order', 'CREATE__FORM__SUBMIT_BTN')
            : Module::t('order', 'UPDATE__FORM__SUBMIT_BTN'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',]
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>
    
    <?= \app\modules\hr\widgets\DictionaryWordCrudWidget::widget() ?>

</div>
