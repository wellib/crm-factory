<?php

use yii\web\View;
use yii\widgets\DetailView;

use app\modules\hr\models\Order;
use app\modules\hr\models\embedded\Hiring;

/* @var $this View */
/* @var $owner Order */
/* @var $model Hiring */

?>

<?= DetailView::widget([
    'model' => $model,
    'template' => "<tr><th width=\"250\">{label}</th><td>{value}</td></tr>",
    'attributes' => [
        'probation_months',
        'employment',
        [
            'attribute' => '__employment_term',
            'value' => $model->getEmploymentTerm(),
        ],
        'date_begin',
        'date_end',
    ],
]) ?>
