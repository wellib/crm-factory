<?php

use yii\web\View;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use app\modules\docs\Module;
use app\modules\docs\models\Contract;

use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\modules\docs\models\Contract */
/* @var $form yii\widgets\ActiveForm */

$datePickerConfig = [
    'pickerButton' => false,
    'pluginOptions' => [
        'autoclose' => true,
        'format' => 'dd.mm.yyyy',
        'daysOfWeekDisabled' => [0,6],
        'todayHighlight' => true,
    ],
];

?>

<div class="contract-form">

    <?php $form = ActiveForm::begin([
        'options' => [
            'enctype' => 'multipart/form-data',
            'data-pjax' => true,
        ]
    ]); ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'number') ?>

    <?= $form->field($model, 'date')->widget(DatePicker::className(), $datePickerConfig) ?>



   <?= $form->field($model, 'company')->widget(Select2::className(), [
                            'theme' => Select2::THEME_DEFAULT,
                            'data' => 
                                ArrayHelper::map(\app\modules\docs\models\Contractcompany::find()->all(), function($model){
                                  return (string) $model->_id;
                                }, function($model){
                                  return implode(' | ', [
                                    $model->name,
                                ]);
                                }),
                            'options' => [
                                'multiple' => true,
                            ],
                            'showToggleAll' => false,
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 0,
                            ],
                        ]) ?>

    <?= $form->field($model, 'names')->textArea(['rows' => '6']) ?>

    <?= $form->field($model, 'parent')->widget(Select2::className(), [
        'theme' => Select2::THEME_DEFAULT,
        'data' => array_merge( array(''=>''),ArrayHelper::map(Contract::find()->all(), function($model){
            return (string) $model->_id;
          }, function($model){
            return implode(' | ', [
              $model->id,
              $model->number,
              $model->name,
              $model->date,
              $model->companyName
            ]);
          })),
        'options' => [
            'multiple' => false,
            'placeholder' => 'Select ...',
        ],
        'showToggleAll' => false,
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 0,
        ],
    ]) ?>
  
  
    <?= $form->field($model, 'attachedFilesUpload[]')->fileInput(['multiple' => true]) ?>
    <table class="table table-bordered table-condensed" style="max-width: 600px;">
        <tbody>
        <?= Html::hiddenInput(Html::getInputName($model, 'files'), null); // hotfix, если удалить все файлы то без него они удалятся из записи в DB ?>
        <?php if (is_array($model->files)): ?>
    <?php foreach ($model->files ? $model->files : [] as $key => $attached_file): ?>
            <tr>
                <td class="vert-align" style="text-align: left;">
                    <?= Html::activeHiddenInput($model, 'files[' . $key . '][filename_orig]', [
                        'value' => $attached_file['filename_orig'],
                    ]) ?>
                    <?= Html::activeHiddenInput($model, 'files[' . $key . '][filename]', [
                        'value' => $attached_file['filename'],
                    ]) ?>
                    <?= $attached_file['filename_orig'] ?>
                </td>
                <td class="vert-align" style="width: 10px;">
                    <button type="button" data-delete-atached-file class="btn btn-danger btn-sm">
                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                    </button>
                    <?php $this->registerJs("
                        $(document).on('click', '[data-delete-atached-file]', function(){
                            $(this).closest('tr').fadeOut(250, function(){ 
                                $(this).remove(); 
                            })
                        });
                    ", \yii\web\View::POS_READY, 'delete-file'); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
        </tbody>
    </table>
    <?php $this->registerCss("
    .table tbody > tr > td.vert-align{
        vertical-align: middle;
    }
    "); ?>

    <?= $form->field($model, 'description')->textArea(['rows' => '6']) ?>

    <?= $form->field($model, 'alls')->checkbox() ?>
  
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
