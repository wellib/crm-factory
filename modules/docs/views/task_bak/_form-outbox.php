<?php

use yii\web\View;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;

use app\modules\docs\Module;
use app\modules\docs\models\Task;

use kartik\select2\Select2;
use kartik\date\DatePicker;


/* @var $this View */
/* @var $model Task */
/* @var $form ActiveForm */

$datePickerConfig = [
    'pickerButton' => false,
    'pluginOptions' => [
        'autoclose' => true,
        'format' => 'dd.mm.yyyy',
        'daysOfWeekDisabled' => [0,6],
        'todayHighlight' => true,
        'startDate' => '+0d', // запрещает использовать "вчера"
    ],
];

?>

<?php Pjax::begin() ?>
<div class="task-form">

    <?php $form = ActiveForm::begin([
        'options' => [
            'enctype' => 'multipart/form-data',
            'data-pjax' => true,
        ]
    ]); ?>

    <?= $form->field($model, 'doc_no') ?>
    <?= $form->field($model, 'date')->widget(DatePicker::className(), $datePickerConfig) ?>
    <?= $form->field($model, '_company')->dropDownList(ArrayHelper::map(\app\modules\docs\models\Company::find()->all(), function($model){
        return (string) $model->_id;
    }, function($model){
        return implode(' | ', [
            $model->name,
        ]);
    })) ?>
    <?= $form->field($model, 'subject') ?>
    <?= $form->field($model, 'doc_to') ?>
    <?= $form->field($model, '_based_on')->dropDownList(ArrayHelper::map(Task::find()->all(), function($model){
        return (string) $model->_id;
    }, function($model){
        return implode(' | ', [
            $model->subject,
            $model->doc_no,
            $model->date,
            $model->doc_from,
            'Название предприятия',
        ]);
    })) ?>




    <?= $form->field($model, 'attachedFilesUpload[]')->fileInput(['multiple' => true]) ?>
    <table class="table table-bordered table-condensed" style="max-width: 600px;">
        <tbody>
        <?= Html::hiddenInput(Html::getInputName($model, '_attached_files'), null); // hotfix, если удалить все файлы то без него они удалятся из записи в DB ?>
        <?php foreach ($model->_attached_files ? $model->_attached_files : [] as $key => $attached_file): ?>
            <tr>
                <td class="vert-align" style="text-align: left;">
                    <?= Html::activeHiddenInput($model, '_attached_files[' . $key . '][filename_orig]', [
                        'value' => $attached_file['filename_orig'],
                    ]) ?>
                    <?= Html::activeHiddenInput($model, '_attached_files[' . $key . '][filename]', [
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
        </tbody>
    </table>
    <?php $this->registerCss("
    .table tbody > tr > td.vert-align{
        vertical-align: middle;
    }
    "); ?>


    <?= $form->field($model, '_outbox_users')->widget(Select2::className(), [
        'theme' => Select2::THEME_DEFAULT,
        'data' => $model->getUsersList(),
        'options' => [
            'multiple' => true,
        ],
        'showToggleAll' => false,
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 0,
        ],
    ]) ?>



    <?= $form->field($model, 'note_text')->widget(\yii\redactor\widgets\Redactor::className(),[
        'clientOptions' => [
            'lang' => substr(Yii::$app->language, 0, 2),
            'imageManagerJson' => false,
            'imageUpload' => false,
            'fileUpload' => false,
            'plugins' => ['fontcolor','table', 'fontfamily'],
            'fontfamily' => 'Times New Roman',
        ],
    ]) ?>

    <?= $form->field($model, 'outbox_status')->radioList(Task::outboxStatus()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Module::t('task', 'CREATE__FORM__SUBMIT_BTN') : Module::t('task', 'UPDATE__FORM__SUBMIT_BTN'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php Pjax::end() ?>