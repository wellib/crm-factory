<?php

use yii\helpers\Html;
use app\modules\docs\Module;


/* @var $this yii\web\View */
/* @var $model app\modules\docs\models\CalendarPeriod */

$this->title = Module::t('calendar_period', 'CREATE__PAGE__TITLE');
$this->params['breadcrumbs'][] = ['label' => Module::t('calendar_period', 'MODEL_NAME_PLURAL'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calendar-period-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
