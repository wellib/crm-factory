<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use app\modules\docs\models\Task;
use yii\helpers\Url;
use kartik\date\DatePicker;
use yii\helpers\Html;
use kartik\select2\Select2;
use app\modules\docs\Module;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\docs\models\TaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$gridColumns = [];

$gridColumns[] = [
    'attribute' => 'id',
    'filter' =>  Html::activeTextInput($searchModel, 'id', [
        'class' => 'form-control',
        'style' => 'width: 60px;'
    ]),
];

$gridColumns[] = 'subject';

$gridColumns[] = [
    'attribute' => 'priority',
    'format' => 'raw',
    'filter' => Html::activeDropDownList($searchModel, 'priority', Task::priorityList(), [
        'class' => 'form-control',
        'prompt' => '',
    ]),
    'value' => function($model) {
        /** @var Task $model */
        return $model->getPriorityLabel();
    },
];


$gridColumns[] = [
    'attribute' => 'users',
    'label' => Module::t('task', 'TASK_SEARCH__ATTR__USERS__LABEL'),
    'format' => 'raw',
    'filter' => Select2::widget([
        'model' => $searchModel,
        'attribute' => 'users',
        'theme' => Select2::THEME_DEFAULT,
        'data' => Task::usersList(),
        'options' => [
            'multiple' => false,
            'prompt' => '',
        ],
        'showToggleAll' => false,
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 0,
        ],
    ]),
    'value' => function($model) use ($searchModel) {
        /** @var Task $model */
        return $this->render('_task-user-list', ['model' => $model, 'searchModel' => $searchModel]);
    },
];
$gridColumns[] = [
    'attribute' => 'deadline_timestamp',
    'format' => 'datetime',
    'filter' => Html::tag('div', DatePicker::widget([
        'model' => $searchModel,
        'attribute' => 'deadline_timestamp',
        'pickerButton' => false,

        //'type' => DatePicker::TYPE_,
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'dd.mm.yyyy',
            'daysOfWeekDisabled' => [0,6],
            'todayHighlight' => true,
            'todayBtn' => true,
        ]
    ]), ['style' => 'width: 150px;']),
];

if (isset($displayStatusColumn) && $displayStatusColumn === true) {
    $gridColumns[] = [
        'attribute' => 'status',
        'format' => 'raw',
        'filter' => Html::activeDropDownList($searchModel, 'status', array_filter(Task::statusList(true), function($key){
            return in_array($key, [
                Task::STATUS__APPROVAL_AWAITING,
                //Task::STATUS_DENIED_APPROVAL,
                Task::STATUS__IN_PROGRESS,
                Task::STATUS__CHECK_RESULTS_AWAITING,
                //Task::STATUS_DISAPPROVE_RESULTS,
                //Task::STATUS_DONE,
            ]);
        }, ARRAY_FILTER_USE_KEY), [
            'class' => 'form-control',
            'prompt' => '',
        ]),
        'value' => function($model) {
            /** @var Task $model */
            return $model->getStatusLabelShort();
        },
    ];
}


$gridColumns[] = [
    'class' => \app\themes\gentelella\widgets\grid\ActionColumn::className(),
    'template' => '{comments} {view}',
    'buttons' => [
        'comments' => function ($url, $model, $key) {
            /** @var Task $model */
            $options = array_merge([
                'title' => Yii::t('yii', 'Update'),
                'aria-label' => Yii::t('yii', 'Update'),
                'data-pjax' => '0',
                'class' => 'btn btn-info btn-sm',
            ], []);
            return Html::a('<span class="glyphicon glyphicon-comment"></span>', ['view2', 'id' => $model->getId()], $options);
        },
    ]
];


?>

<?php //Pjax::begin(); ?>
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
        'columns' => $gridColumns,
    ]); ?>
</div>
<?php //Pjax::end(); ?>
<?php $this->registerJs("

$(document).on('click', '._grid tbody tr td', function(e){
    var el = $(this);
    if (!el.is(':last-child')) {
        location.href = el.closest('tr').data('url');
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
