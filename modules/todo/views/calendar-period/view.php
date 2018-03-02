<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\todo\Module;

/* @var $this yii\web\View */
/* @var $model app\modules\todo\models\CalendarPeriod */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Module::t('calendar_period', 'MODEL_NAME_PLURAL'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calendar-period-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Module::t('calendar_period', 'UPDATE___LINK__LABEL'), ['update', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Module::t('calendar_period', 'DELETE___LINK__LABEL'), ['delete', 'id' => (string)$model->_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Module::t('calendar_period', 'DELETE___LINK__CONFIRM_MESSAGE'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'template' => "<tr><th width=\"225\">{label}</th><td>{value}</td></tr>",
        'attributes' => [
            //'_id',
            'name',
            //'from_date:date',
            //'to_date:date',
            [
                'attribute' => 'from_date',
                'value' => $model->getFromDateFormat(),
            ],
            [
                'attribute' => 'to_date',
                'value' => $model->getToDateFormat(),
            ],
            'every_year:boolean',
            //'type',
            [
                'attribute' => 'type',
                'value' => $model->getTypeLabel(),
            ],
            //'created_at',
            //'updated_at',
        ],
    ]) ?>

</div>
