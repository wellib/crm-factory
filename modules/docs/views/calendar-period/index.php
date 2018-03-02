<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;
use app\modules\docs\Module;
use app\modules\docs\models\CalendarPeriod;
use app\modules\docs\models\CalendarPeriodSearch;

/* @var $this yii\web\View */
/* @var $searchModel CalendarPeriodSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('calendar_period', 'MODEL_NAME_PLURAL');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calendar-period-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Module::t('calendar_period', 'CREATE___LINK__LABEL'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'name',
            [
                'attribute' => 'from_date',
                'filter' => Html::tag('div', DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'from_date',
                    'pickerButton' => false,

                    //'type' => DatePicker::TYPE_,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd.mm.yyyy',
                        //'daysOfWeekDisabled' => [0,6],
                        'todayHighlight' => true,
                        //'todayBtn' => true,
                    ]
                ]), ['style' => 'width: 150px;']),
                'value' => function($model) {
                    /** @var CalendarPeriod $model */
                    return $model->getFromDateFormat();
                }
            ],
            [
                'attribute' => 'to_date',
                'filter' => Html::tag('div', DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'to_date',
                    'pickerButton' => false,

                    //'type' => DatePicker::TYPE_,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd.mm.yyyy',
                        //'daysOfWeekDisabled' => [0,6],
                        'todayHighlight' => true,
                        //'todayBtn' => true,
                    ]
                ]), ['style' => 'width: 150px;']),
                'value' => function($model) {
                    /** @var CalendarPeriod $model */
                    return $model->getToDateFormat();
                }
            ],
            [
                'attribute' => 'type',
                'filter' => Html::activeDropDownList($searchModel, 'type', $searchModel->getTypeList(), [
                    'prompt' => '',
                    'class' => 'form-control',
                ]),
                'value' => function($model) {
                    /** @var CalendarPeriod $model */
                    return $model->getTypeLabel();
                }
            ],
            //[
            //    'attribute' => 'every_year',
            //    'format' => 'boolean',
            //    'filter' => Html::activeDropDownList($searchModel, 'every_year', $searchModel->getEveryYearDropDownOptions(), [
            //        'prompt' => '',
            //        'class' => 'form-control',
            //    ]),
            //],
            // 'created_at',
            // 'updated_at',

            ['class' => \app\themes\gentelella\widgets\grid\ActionColumn::className()],
        ],
    ]); ?>
</div>
<?php $this->registerCss("
.table-hover-custom > tbody > tr:hover {
    cursor: pointer;
}
.input-group {
    margin-bottom: 0;
}
")?>