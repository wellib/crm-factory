<?php

use yii\helpers\Html;
use app\themes\gentelella\widgets\Panel;
use yii\widgets\Pjax;
//use yii\grid\GridView;
use app\modules\docs\Module;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use yii\helpers\Url;
use app\modules\docs\models\Task;
use app\modules\docs\models\TaskSearch;
use kartik\export\ExportMenu;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel TaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Входящие документы';

$columns = [
    [
        'attribute' => 'id',
        'filter' =>  Html::activeTextInput($searchModel, 'id', [
            'class' => 'form-control',
            'style' => 'width: 60px;'
        ]),
    ],
    'doc_no',
    'date',
    '_company',
    'subject',
    'doc_from',
    'inbox_status',

    //[
    //    'attribute' => '_author',
    //    //'label' => Module::t('task', 'TASK_SEARCH__ATTR__USERS__LABEL'),
    //    'format' => 'raw',
    //    'filter' => Select2::widget([
    //        'model' => $searchModel,
    //        'attribute' => '_author',
    //        'theme' => Select2::THEME_DEFAULT,
    //        'data' => Task::usersList(),
    //        'options' => [
    //            'multiple' => false,
    //            'prompt' => '',
    //        ],
    //        'showToggleAll' => false,
    //        'pluginOptions' => [
    //            'allowClear' => true,
    //            'minimumInputLength' => 0,
    //        ],
    //    ]),
    //    'value' => function($model) use ($searchModel) {
    //        /** @var Task $model */
    //        return $model->author->getNameAndPosition();
    //        //return $this->render('_task-user-list', ['model' => $model, 'searchModel' => $searchModel]);
    //    },
    //],
    //[
    //    'attribute' => '_users_approve_execute',
    //    //'label' => Module::t('task', 'TASK_SEARCH__ATTR__USERS__LABEL'),
    //    'format' => 'raw',
    //    'filter' => Select2::widget([
    //        'model' => $searchModel,
    //        'attribute' => '_users_approve_execute',
    //        'theme' => Select2::THEME_DEFAULT,
    //        'data' => Task::usersList(),
    //        'options' => [
    //            'multiple' => false,
    //            'prompt' => '',
    //        ],
    //        'showToggleAll' => false,
    //        'pluginOptions' => [
    //            'allowClear' => true,
    //            'minimumInputLength' => 0,
    //        ],
    //    ]),
    //    'value' => function($model) use ($searchModel) {
    //        /** @var Task $model */
    //        return implode(', ', array_map(function($model){
    //            /** @var \app\modules\accounts\models\User $model */
    //            return $model->getNameAndPosition();
    //        }, $model->usersApproveExecute));
    //        //return $this->render('_task-user-list', ['model' => $model, 'searchModel' => $searchModel]);
    //    },
    //],
    //[
    //    'attribute' => '_users_check_result',
    //    //'label' => Module::t('task', 'TASK_SEARCH__ATTR__USERS__LABEL'),
    //    'format' => 'raw',
    //    'filter' => Select2::widget([
    //        'model' => $searchModel,
    //        'attribute' => '_users_check_result',
    //        'theme' => Select2::THEME_DEFAULT,
    //        'data' => Task::usersList(),
    //        'options' => [
    //            'multiple' => false,
    //            'prompt' => '',
    //        ],
    //        'showToggleAll' => false,
    //        'pluginOptions' => [
    //            'allowClear' => true,
    //            'minimumInputLength' => 0,
    //        ],
    //    ]),
    //    'value' => function($model) use ($searchModel) {
    //        /** @var Task $model */
    //        return implode(', ', array_map(function($model){
    //            /** @var \app\modules\accounts\models\User $model */
    //            return $model->getNameAndPosition();
    //        }, $model->usersCheckResult));
    //        //return $this->render('_task-user-list', ['model' => $model, 'searchModel' => $searchModel]);
    //    },
    //],
    //[
    //    'attribute' => '_users_performers',
    //    //'label' => Module::t('task', 'TASK_SEARCH__ATTR__USERS__LABEL'),
    //    'format' => 'raw',
    //    'filter' => Select2::widget([
    //        'model' => $searchModel,
    //        'attribute' => '_users_performers',
    //        'theme' => Select2::THEME_DEFAULT,
    //        'data' => Task::usersList(),
    //        'options' => [
    //            'multiple' => false,
    //            'prompt' => '',
    //        ],
    //        'showToggleAll' => false,
    //        'pluginOptions' => [
    //            'allowClear' => true,
    //            'minimumInputLength' => 0,
    //        ],
    //    ]),
    //    'value' => function($model) use ($searchModel) {
    //        /** @var Task $model */
    //        return implode(', ', array_map(function($model){
    //            /** @var \app\modules\accounts\models\User $model */
    //            return $model->getNameAndPosition();
    //        }, $model->usersPerformers));
    //        //return $this->render('_task-user-list', ['model' => $model, 'searchModel' => $searchModel]);
    //    },
    //],

    //[
    //    'attribute' => 'users',
    //    'label' => Module::t('task', 'TASK_SEARCH__ATTR__USERS__LABEL'),
    //    'format' => 'raw',
    //    'filter' => Select2::widget([
    //        'model' => $searchModel,
    //        'attribute' => 'users',
    //        'theme' => Select2::THEME_DEFAULT,
    //        'data' => $searchModel->getUsersList(),
    //        'options' => [
    //            'multiple' => false,
    //            'prompt' => '',
    //        ],
    //        'showToggleAll' => false,
    //        'pluginOptions' => [
    //            'allowClear' => true,
    //            'minimumInputLength' => 0,
    //        ],
    //    ]),
    //    'value' => function($model) use ($searchModel) {
    //        /** @var Task $model */
    //        return $this->render('_task-user-list', ['model' => $model, 'searchModel' => $searchModel]);
    //    },
    //],
    //'deadline_timestamp:datetime',
    //[
    //    'attribute' => 'deadline_timestamp',
    //    'format' => 'datetime',
    //    'filter' => Html::tag('div', DatePicker::widget([
    //        'model' => $searchModel,
    //        'attribute' => 'deadline_timestamp_from',
    //        'attribute2' => 'deadline_timestamp_to',
    //        'pickerButton' => false,
    //
    //        'type' => DatePicker::TYPE_RANGE,
    //        'pluginOptions' => [
    //            'autoclose' => true,
    //            'format' => 'dd.mm.yyyy',
    //            'daysOfWeekDisabled' => [0,6],
    //            'todayHighlight' => true,
    //            'todayBtn' => true,
    //        ]
    //    ]), ['style' => 'width: 230px;']),
    //],
    //[
    //    'attribute' => 'status',
    //    'format' => 'raw',
    //    'filter' => Html::activeDropDownList($searchModel, 'status', array_filter(Task::statusList(true), function($key){
    //        return in_array($key, [
    //            Task::STATUS__APPROVAL_AWAITING,
    //            Task::STATUS__APPROVAL_FAILED,
    //            Task::STATUS__IN_PROGRESS,
    //            Task::STATUS__CHECK_RESULTS_AWAITING,
    //            //Task::STATUS_DISAPPROVE_RESULTS,
    //            Task::STATUS__DONE,
    //        ]);
    //    }, ARRAY_FILTER_USE_KEY), [
    //        'class' => 'form-control',
    //        'prompt' => '',
    //    ]),
    //    'value' => function($model) {
    //        /** @var Task $model */
    //        return $model->getStatusLabelShort();
    //    },
    //],


];

$columns[] = [
    'class' => \app\themes\gentelella\widgets\grid\ActionColumn::className(),
    //'template' => '{view} {update} {delete}',
    //'buttons' => [
    //    'comments' => function ($url, $model, $key) {
    //        /** @var Task $model */
    //        $options = array_merge([
    //            'title' => Yii::t('yii', 'Update'),
    //            'aria-label' => Yii::t('yii', 'Update'),
    //            'data-pjax' => '0',
    //            'class' => 'btn btn-info btn-sm',
    //        ], []);
    //        return Html::a('<span class="glyphicon glyphicon-comment"></span>', ['view2', 'id' => $model->getId()], $options);
    //    },
    //]
];

?>


<?php Panel::begin(); ?>

<h1><?= Html::encode($this->title) ?></h1>


<?= ExportMenu::widget([
    'dataProvider' => $dataProvider,
    'columns' => $columns,
    'target' => ExportMenu::TARGET_SELF,
    'showConfirmAlert' => false,
    'exportConfig' => [
        ExportMenu::FORMAT_TEXT => false,
        ExportMenu::FORMAT_PDF => false,
        ExportMenu::FORMAT_HTML => false,
        ExportMenu::FORMAT_CSV => false,
    ],
]); ?>

<p>
    <?= Html::a('Добавить', ['create', 'scenario' => Task::SCENARIO_INBOX], ['class' => 'btn btn-success']) ?>
</p>

<div class="table-responsive">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'table table-bordered table-hover table-hover-custom _grid',
        ],
        'rowOptions' => function ($model, $key, $index, $grid) {
            /** @var Task $model */
            return ['data-url' => Url::to(['view', 'id' => (string) $model->_id, '#' => 'outbox'])];
        },
        'columns' => $columns,
    ]); ?>
</div>

<?php $this->registerJs("

$(document).on('click', '._grid tbody tr td', function(e){
    var el = $(this);
    if (!el.is(':last-child')) {
        //location.href = el.closest('tr').data('url');
    }
});

")?>
<?php $this->registerCss("
.table-hover-custom > tbody > tr:hover {
    cursor: pointer;
}
.input-group {
    margin-bottom: 0;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    padding-top: 3px;
}
.select2-container--default .select2-selection--single, .select2-container--default .select2-selection--multiple {
    min-height: 34px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow b {
    margin-top: -4px;
}
")?>
<?php Panel::end(); ?>

