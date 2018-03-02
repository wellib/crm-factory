<?php

use yii\web\View;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use yii\data\ActiveDataProvider;

use app\modules\hr\Module;
use app\modules\hr\models\Employee;
use app\modules\hr\models\Order;
use app\modules\hr\models\OrderSearch;

use app\themes\gentelella\widgets\Panel;
use app\themes\gentelella\widgets\grid\ActionColumn;

//use yii\grid\GridView;

use kartik\grid\GridView;
use kartik\export\ExportMenu;

use yii\bootstrap\ButtonDropdown;

use kartik\date\DatePicker;

/* @var $this View */
/* @var $searchModel OrderSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Module::t('order', 'MODEL_NAME_PLURAL');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Panel::begin(); ?>

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= ButtonDropdown::widget([
            'label' => Module::t('order', 'CREATE___LINK__LABEL'),
            'options' => ['class' => 'btn btn-success'],
            'dropdown' => [
                'items' => call_user_func(function () {
                    $items = [];
                    foreach (Order::typesLabels() as $type => $label) {
                        $items[] = [
                            'label' => $label,
                            'url' => ['/hr/order/create', 'type' => $type],
                        ];
                    }
                    return $items;
                }),
            ],
        ]) ?>
        <?= ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => call_user_func(function () {
                /** @var Order $model */
                $columns = Order::exportColumnsConfig();
                foreach (Order::getEmbeddedModelsConfig() as $embeddedModelConfig) {
                    $emc = $embeddedModelConfig;
                    if (method_exists($emc['className'], 'exportColumnsConfig')) {
                        $columns = ArrayHelper::merge(
                            $columns,
                            $emc['className']::exportColumnsConfig($emc['modelAttribute'])
                        );
                    }
                }
                return $columns;
            }),
            'target' => ExportMenu::TARGET_SELF,
            'showConfirmAlert' => false,
            'exportConfig' => [
                ExportMenu::FORMAT_TEXT => false,
                ExportMenu::FORMAT_PDF => false,
                ExportMenu::FORMAT_HTML => false,
                ExportMenu::FORMAT_CSV => false,
            ],
        ]); ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'type',
                'value' => function($model) {
                    /** @var Order $model */
                    return $model->getTypeLabel();
                },
                'filter' => Html::activeDropDownList($searchModel, 'type', Order::typesLabels(), [
                    'class' => 'form-control',
                    'prompt' => '',
                ]),
            ],
            [
                'attribute' => '_employees',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var Order $model */
                    return $this->render('_employees', ['model' => $model]);
                },
            ],
            [
                'attribute' => 'structure_department',
                'label' => \app\modules\structure\Module::t('department', 'MODEL_NAME'),
                'filter' => \app\modules\structure\widgets\DepartmentInputWidget::widget([
                    'model' => $searchModel,
                    'attribute' => 'structure_department',
                ]),
                'format' => 'raw',
                'value' => function($model) {
                    /** @var Order $model */
                    $items = [];
                    foreach ($model->employees as $employee) {
                        $employee->companyCard->getStructureDepartment(true, true);
                        $items[(string) $employee->companyCard->_structure_department] = $employee->companyCard->getStructureDepartment(true, true);
                    }
                    if ($items > 0) {
                        return implode('<br>', $items);
                    }
                    return null;
                },
            ],
            'number',
            [
                'attribute' => 'date',
                'filter' => Html::tag('div', DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'date_from',
                    'attribute2' => 'date_to',
                    'pickerButton' => false,
                    'layout' => '<span class="input-group-addon kv-field-separator">от</span>{input1}<span class="input-group-addon kv-field-separator">до</span>{input2}',
                    'type' => DatePicker::TYPE_RANGE,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd.mm.yyyy',
                        //'daysOfWeekDisabled' => [0,6],
                        'todayHighlight' => true,
                        'todayBtn' => true,
                    ]
                ]), ['style' => 'width: 230px;']),
            ],
            ['class' => ActionColumn::className()],
        ],
    ]); ?>
<?php Panel::end(); ?>

