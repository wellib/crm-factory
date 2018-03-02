<?php

use yii\web\View;
use yii\web\JsExpression;

use yii\helpers\Html;

use yii\bootstrap\ActiveForm;

use app\modules\structure\Module;
use app\modules\structure\models\Department;

use kartik\select2\Select2;

/* @var $this View */
/* @var $model Department */
/* @var $form ActiveForm */

?>


<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'name') ?>

<?= $form->field($model, 'icon')->widget(Select2::className(), [
    'theme' => Select2::THEME_DEFAULT,
    'data' => Department::iconsList(),
    'theme' => Select2::THEME_DEFAULT,

    'options' => [
        'placeholder' => '',
        'multiple' => false,
        'prompt' => '',
    ],
    'pluginOptions' => [
        'allowClear' => true,
        'templateResult' => new JsExpression(<<<JS
            function formatState (state) {
                if (!state.id) { 
                    return state.text; 
                }
                var st = $(
                        '<span><i class="fa fa-' + state.text + '" aria-hidden="true"></i> ' + state.text + '</span>'
                );
                return st;
            }
JS
        )
//        'minimumInputLength' => 0,
//        'language' => 'ru',
//        'ajax' => [
//            'url' => Url::to(['/hr/employee/select2']),
//            'dataType' => 'json',
//            'delay' => 250,
//            'data' => new JsExpression('function(params) { return { term:params.term, page: params.page}; }'),
//            'processResults' => new JsExpression(<<< JS
//                    function (data, params) {
//                        params.page = params.page || 1;
//                        return {
//                            results: data.items,
//                            pagination: {
//                                more: (params.page * {$resultsPerRequest}) < data.total_count
//                            }
//                        };
//                    }
//JS
//            ),
//            'cache' => true
//
//        ],
//        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
//        'templateResult' => new JsExpression('function(employee) { return employee.text; }'),
//        'templateSelection' => new JsExpression('function (employee) { return employee.text; }'),
    ],

]) ?>

<?= $form->field($model, '_parent')->dropDownList($model->getParentDropDownListOptions('—'), [
    'prompt' => '',
    'options' => $model->getParentDropDownListDisabledOptions(),
]) ?>

<div class="form-group">
    <?= Html::submitButton($model->isNewRecord
        ? Module::t('department', 'CREATE__FORM__SUBMIT_BTN')
        : Module::t('department', 'UPDATE__FORM__SUBMIT_BTN'),
        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
    ) ?>
</div>

<?php ActiveForm::end(); ?>
