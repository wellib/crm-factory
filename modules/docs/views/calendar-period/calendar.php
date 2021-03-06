<?php

use yii\web\View;
use app\modules\docs\models\CalendarPeriod;
use app\modules\docs\widgets\CalendarWidget;
use yii\widgets\Pjax;

/* @var $this View*/
/* @var $month string*/
/* @var $year string*/

?>
<div class="row">
    <div class="col-md-6">
        <? //= CalendarPeriod::buildCalendar($month, $year) ?>
        <?php Pjax::begin(); ?>
        <?= CalendarWidget::widget([
            'month' => $month,
            'year' => $year,
        ]) ?>
        <?php Pjax::end(); ?>
    </div>
</div>
