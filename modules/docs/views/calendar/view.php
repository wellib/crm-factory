<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\docs\models\Calendar */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Ежедневник', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calendar-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => (string)$model->_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Уверены что хотите удалить?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'date',
            'date_to',
            'type',
            'description',
            [
                'attribute' => 'status',
                'value' => $model->getStatusName(),
            ],
            //'_author',
            //'created_at',
            //'updated_at',
        ],
    ]) ?>

</div>
