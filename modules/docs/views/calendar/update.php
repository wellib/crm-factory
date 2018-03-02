<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\docs\models\Calendar */

$this->title = 'Редактировать событие';
$this->params['breadcrumbs'][] = ['label' => 'Ежедневник', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => (string)$model->_id]];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="calendar-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
