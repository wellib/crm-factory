<?php

use yii\web\View;
use yii\widgets\DetailView;

use app\modules\hr\models\Order;
use app\modules\hr\models\embedded\BusinessTrip;

/* @var $this View */
/* @var $owner Order */
/* @var $model BusinessTrip */

?>

<?= DetailView::widget([
    'model' => $model,
    'template' => "<tr><th width=\"250\">{label}</th><td>{value}</td></tr>",
    'attributes' => [
        'duration_in_days',
        'date_begin',
        'date_end',
        'location',
        'organization',
        'purpose',
        'based_on',
    ],
]) ?>
