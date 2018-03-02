<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\docs\Module;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\docs\models\TemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Шаблоны документов';
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
            [
                'attribute' => 'date',
                'format' => 'date',
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
                        'daysOfWeekDisabled' => [0,6],
                        'todayHighlight' => true,
                        'todayBtn' => true,
                    ]
                ]), ['style' => 'width: auto;']),
            ],

        ];
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
                            'class' => 'btn btn-danger  btn-sm',
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
<div class="template-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
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
