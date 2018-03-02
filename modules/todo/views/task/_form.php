<?php

use yii\web\View;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;

use app\modules\todo\Module;
use app\modules\todo\models\Task;

use kartik\select2\Select2;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;

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
        'startDate' =>  date('d-m-Y'),
    ],
];
$dateTimePickerConfig = [
		'options' => ['placeholder' => 'Дата и время'],
		'pluginOptions' => [
				'autoclose' => true,
				'format' => 'dd.mm.yyyy hh:ii',
				'daysOfWeekDisabled' => [0,6],
				'todayHighlight' => true,
				'startDate' => date('Y-m-d'), 
				'weekStart' => 1,
				'hoursDisabled' => '0,1,2,3,4,5,6,7,21,22,23'
				]
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



    <?php if ($model->isNewRecord): ?>
        <?= $form->field($model, 'subject') ?>
    <?php endif; ?>



    <?php if ($model->isNewRecord): ?>
    <?= $form->field($model, 'description')->widget(\yii\redactor\widgets\Redactor::className(),[
        'clientOptions' => [
            'lang' => substr(Yii::$app->language, 0, 2),
            'imageManagerJson' => false,
            'imageUpload' => false,
            'fileUpload' => false,
            'plugins' => ['fontcolor','table', 'fontfamily'],
            'fontfamily' => 'Times New Roman',
        ],
    ]) ?>
    <?php endif; ?>
    <?= $this->render('_redactor-editor-styles') ?>


    <?= $form->field($model, 'priority')->dropDownList($model->getPriorityList()) ?>


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

		<?php if ($model->isNewRecord || Yii::$app->getUser()->getIdentity()->nickname === 'root'): ?>
				<?= $form->field($model, 'deadline_type')->dropDownList($model->getDeadlineTypeList(), [
						'onchange' => 'this.form.submit()',
				]) ?>
		<?php endif; ?>

    <?php if (in_array($model->getScenario(), [Task::DEADLINE_TYPE__EVERY_WEEK])): ?>
        <?= $form->field($model, 'deadline_every_week')->inline()->checkboxList($model->getDeadlineWeekDaysList()) ?>
    <?php endif; ?>

    <?php if (in_array($model->getScenario(), [Task::DEADLINE_TYPE__EVERY_MONTH])): ?>
        <?= $form->field($model, 'deadline_every_month')->inline()->checkboxList($model->getDeadlineMonthDaysList())->inline() ?>
    <?php endif; ?>

    <?php if (in_array($model->getScenario(), [Task::DEADLINE_TYPE__EVERY_DATE])): ?>

        <?= $form->field($model, 'deadline_every_date')->hiddenInput([
            'value' => 'ok'
        ]) ?>
        <div id="every-date-list">
            <?php if (is_array($model->deadline_every_date)): ?>
                <?php foreach ($model->deadline_every_date as $everyDateKey => $everyDate): ?>
                    <?= $this->render('_form-deadline_every_date-item', [
                        'model'            => $model,
                        'form'             => $form,
                        'key'              => $everyDateKey,
                        'datePickerConfig' => $datePickerConfig,
                    ]) ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <script type="text/template" id="every-date-template">
            <?= $this->render('_form-deadline_every_date-item', [
                'model'            => $model,
                'form'             => $form,
                'key'              => '__KEY__',
                'datePickerConfig' => $datePickerConfig,
            ]) ?>
        </script>

        <div>
            <?php

            $this->registerJs(<<<JS
            everyDateAdd = function() {
                var template = $('#every-date-template').html(),
                    listBody = $('#every-date-list'),
                    lastEl = listBody.find('> [data-key]').last(),
                    nextKey = lastEl.length > 0 ? parseInt(lastEl.data('key')) + 1 : 0,
                    newEl = $(template.replace(/__KEY__/g, nextKey));
                newEl.find('.has-error').removeClass('has-error');
                newEl.find('.help-block-error').html('');
                
                listBody.append(newEl);
                var input = newEl.find('[data-krajee-kvDatepicker]'),
                    config = window[input.attr('data-krajee-kvDatepicker')];
                input.kvDatepicker(config);
                // custom code(inits) here
            };
JS
, View::POS_READY, 'everyDateAdd')?>
            <button type="button" class="btn btn-primary btn-sm" onclick="everyDateAdd();">
                Добавить дату
            </button>
        </div>
    <?php endif; ?>


    <?php if (in_array($model->getScenario(), [Task::DEADLINE_TYPE__EVERY_DAY, Task::DEADLINE_TYPE__EVERY_WEEK, Task::DEADLINE_TYPE__EVERY_MONTH])): ?>
        <?//= $form->field($model, 'start_date')->widget(DatePicker::className(), $datePickerConfig) ?>
				<?= $form->field($model, 'start_date')->widget(DateTimePicker::classname(), $dateTimePickerConfig);?>
				<?= $form->field($model, 'deadline_date')->widget(DateTimePicker::classname(), $dateTimePickerConfig);?>
        <? //= $form->field($model, 'start_time')->dropDownList($model->getTimeList()) ?>
    <?php endif; ?>

    <?php if (in_array($model->getScenario(), [Task::DEADLINE_TYPE__ONE_TIME, Task::DEADLINE_TYPE__EVERY_DATE])): ?>
        <?//= $form->field($model, 'perform_date')->widget(DatePicker::className(), $datePickerConfig) ?>
				<?= $form->field($model, 'perform_date')->widget(DateTimePicker::classname(), $dateTimePickerConfig);?>
        <? //= $form->field($model, 'deadline_date')->widget(DatePicker::className(), $datePickerConfig) ?>
				<?= $form->field($model, 'deadline_date')->widget(DateTimePicker::classname(), $dateTimePickerConfig);?>
    <?php endif; ?>

    <?php if (in_array($model->getScenario(), [Task::DEADLINE_TYPE__EVERY_WEEK, Task::DEADLINE_TYPE__EVERY_DAY, Task::DEADLINE_TYPE__EVERY_MONTH])): ?>
        <? //= $form->field($model, 'end_date')->widget(DatePicker::className(), $datePickerConfig) ?>
				<?= $form->field($model, 'end_date')->widget(DatePicker::classname(), $datePickerConfig);?>
    <?php endif; ?>



		<?php if ($model->isNewRecord || Yii::$app->getUser()->getIdentity()->nickname === 'root'): ?>

    <?= $form->field($model, '_users_performers')->widget(Select2::className(), [
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
    ])->hint(Module::t('task', 'ATTR__USERS_PERFORMERS__HINT')) ?>

    <div class="form-group">
        <div class="accordion" id="accordion" role="tablist" aria-multiselectable="true">
            <div class="panel">
                <a class="panel-heading collapsed" role="tab" id="headingTwo" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    <h4 class="panel-title"><?= Module::t('task', 'USER_ROLES_IN_TASK') ?></h4>
                </a>
                <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo" aria-expanded="false">
                    <div class="panel-body">


                        <?= $form->field($model, '_users_approve_execute')->widget(Select2::className(), [
                            'theme' => Select2::THEME_DEFAULT,
                            'data' => $model->getUsersList(true),
                            'options' => [
                                'multiple' => true,
                            ],
                            'showToggleAll' => false,
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 0,
                            ],
                        ]) ?>

                        <?= $form->field($model, '_users_notify_after_finished')->widget(Select2::className(), [
                            'theme' => Select2::THEME_DEFAULT,
                            'data' => $model->getUsersList(true),
                            'options' => [
                                'multiple' => true,
                            ],
                            'showToggleAll' => false,
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 0,
                            ],
                        ]) ?>

                        <?= $form->field($model, '_users_check_result')->widget(Select2::className(), [
                            'theme' => Select2::THEME_DEFAULT,
                            'data' => $model->getUsersList(true),
                            'options' => [
                                'multiple' => true,
                            ],
                            'showToggleAll' => false,
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 0,
                            ],
                        ])->hint(Module::t('task', 'ATTR__USERS_CONTROL_RESULTS__HINT')) ?>


                    </div>
                </div>
            </div>
        </div>
    </div>
		<?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Module::t('task', 'CREATE__FORM__SUBMIT_BTN') : Module::t('task', 'UPDATE__FORM__SUBMIT_BTN'), [
            'class' => $model->isNewRecord ? 'submit-button btn btn-success' : 'btn btn-primary'
        ]) ?>
    </div>

<?php
$this->registerJs(<<<JS
$('.submit-button').on('click', function (e) {
    var button = $(this);
    if (button.data('brother') == undefined) {
        var brother = $(document.createElement(button[0].tagName));
        brother.html('Пожалуйста подождите...');
        brother.attr('disabled', true);
        brother.addClass('disabled');
        brother.addClass(button.attr('class'));
        brother.hide();
        brother.insertAfter(button);
        button.data('brother', brother)
    }else{
        var brother = button.data('brother');
    }

    if (button.css('display') !== 'none') {
        brother.show();
        button.hide();
        setTimeout(function () {
            brother.hide();
            button.show();
        }, 1000);
    }
});
JS
)?>

    <?php ActiveForm::end(); ?>

</div>
<?php Pjax::end() ?>
