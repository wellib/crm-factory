<?php

use yii\helpers\Html;
use app\modules\docs\Module;

/* @var $this yii\web\View */
/* @var $model app\modules\docs\models\CalendarPeriod */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Module::t('calendar_period', 'MODEL_NAME_PLURAL'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => (string)$model->_id]];
$this->params['breadcrumbs'][] = Module::t('calendar_period', 'UPDATE__PAGE__TITLE');
?>
<div class="calendar-period-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
