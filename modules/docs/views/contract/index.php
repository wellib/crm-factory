<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\docs\Module;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
use kartik\export\ExportMenu;


$this->title = 'Реестр договоров';
$this->params['breadcrumbs'][] = $this->title;
$company_list = array_merge(array('0' => 'Все'), ArrayHelper::map(\app\modules\docs\models\Company::find()->all(), function($model){
																return (string) $model->_id;
															}, function($model){
																	return implode(' | ', [
																			$model->name,
																	]);
															}));
$columns = [
            'id',
            'name',
            'number',
						'names',
						[
								'attribute' => 'date',
								'format' => 'date',
								'filter' => Html::tag('div', DatePicker::widget([
										'model' => $searchModel,
										'attribute' => 'date_from',
										'attribute2' => 'date_to',
										'pickerButton' => false,
										'layout' => '<div class="clearfix"><span class="input-group-addon kv-field-separator">от</span>{input1}</div><div class="clearfix"><span class="input-group-addon kv-field-separator">до</span>{input2}</div>',

										'type' => DatePicker::TYPE_RANGE,
										'pluginOptions' => [
												'autoclose' => true,
												'format' => 'dd.mm.yyyy',
												'daysOfWeekDisabled' => [0,6],
												'todayHighlight' => true,
												'todayBtn' => true,
										]
								]), ['style' => '']),
						],
            [
								'attribute' => 'company',
								'format' => 'raw',
								'filter' => Select2::widget([
										'model' => $searchModel,
										'attribute' => 'company',
										'theme' => Select2::THEME_DEFAULT,
										'data' => $company_list,
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
										return $model->companyName;
								},
						],
        ];
	
?>
<?php
$columnsexport = [
            'id',
            'name',
            'number',
            'date',
						[
								'attribute'=>'company',
								'label'=>Yii::t('app', 'Предприятие'),
								'value'=>function ($model, $key, $index, $widget) { 
										return isset($model->companyName)?$model->companyName:'';
								},
								'format'=>'raw'
						],
            'names',
            [
                    'format' => 'raw',
                    'label' => Yii::t('app', 'Привязка'),
                    'value' => function ($model, $key, $index, $widget) { 
												return '<a target="_blank" href="'. Url::to(['/docs/contract/view', 'id' => (string) $model->parent]).'">'.
												(isset($model->parentContract)?$model->parentContract:'').'</a>';
										},
            ],
            'description',
            [
                    'format' => 'raw',
                    'label' => Yii::t('app', 'Кто добавил'),
                    'value' => function ($model, $key, $index, $widget) { return isset($model->author)?$model->author:'';},
            ],
            'created_at:datetime',
            'updated_at:datetime',

        ];
?>
<?= ExportMenu::widget([
    'dataProvider' => $dataProvider,
    'columns' => $columnsexport,
    'target' => ExportMenu::TARGET_SELF,
    'showConfirmAlert' => false,
    'exportConfig' => [
        ExportMenu::FORMAT_TEXT => false,
        ExportMenu::FORMAT_PDF => false,
        ExportMenu::FORMAT_HTML => false,
        ExportMenu::FORMAT_CSV => false,
    ],
]); ?>

<?php
		
$columns[] = [
    'class' => \app\themes\gentelella\widgets\grid\ActionColumn::className(),
		'template' => '{view} {update} {delete} ',
                'buttons' => [
                    'update' => function ($url, $model, $key) {
						if ($model->status >= 1) return '';
						$options = array_merge([
                            'title' => Yii::t('yii', 'Обновить'),
                            'aria-label' => Yii::t('yii', 'Обновить'),
                            'data-pjax' => '0',
                            'class' => 'btn btn-success btn-sm',
                        ], []);
						return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['view', 'id' => $model->getId()], $options);
                    },
                    'delete' => function ($url, $model, $key) {
						if ($model->status >= 1) return '';
						$options = array_merge([
                            'title' => Yii::t('yii', 'Удалить'),
                            'aria-label' => Yii::t('yii', 'Удалить'),
                            'data-pjax' => '0',
                            'class' => 'btn btn-info btn-danger btn-sm',
				            'data' => [
								'confirm' => 'Вы уверены, что хотите удалить?',
								'method' => 'post',
							],
                        ], []);
						return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $model->getId()], $options);
                    },

				]
];	
?>
<div class="contract-index">
<div class="x_panel">
<div class="x_content">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <p>
	<ul class="nav nav-tabs">
		<li><?= Html::a('Таблицей', ['index'], ['class' => '']) ?></li>
		<li><?= Html::a('Деревом', ['tree'], ['class' => '']) ?></li>
		<li><?= Html::a('Компании', ['/docs/contractcompany/index'], ['class' => '']) ?></li>
	</ul>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function ($model, $key, $index, $grid) {
            if (!($model->status > 0))
							return ['class' => 'red'];
        },
        'columns' => $columns,
    ]); ?>
</div>
</div>
</div>
