<?php

use yii\web\View;
use yii\widgets\DetailView;

use app\modules\hr\models\Order;
use app\modules\hr\models\embedded\Fired;

/* @var $this View */
/* @var $owner Order */
/* @var $model Fired */

?>

<?= DetailView::widget([
    'model' => $model,
    'template' => "<tr><th width=\"250\">{label}</th><td>{value}</td></tr>",
    'attributes' => [
        'date',
        'based_on_documents',
        [
            'attribute' => '__base_dismissal',
            'value' => $model->getBaseDismissal(),
        ],
    ],
]) ?>
