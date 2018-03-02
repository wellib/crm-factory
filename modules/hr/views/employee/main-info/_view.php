<?php

use yii\web\View;
use yii\widgets\DetailView;

use app\modules\hr\models\Employee;

/* @var $this View */
/* @var $model Employee */

?>

<?= DetailView::widget([
    'model' => $model,
    'template' => "<tr><th width=\"225\">{label}</th><td>{value}</td></tr>",
    'attributes' => [
        'first_name',
        'last_name',
        'middle_name',
        [
            'attribute' => 'sex',
            'value' => $model->getSexLabel(),
        ],
        'birthday',
        [
            'attribute' => '_user',
            'format' => 'raw',
            'value' => $model->getUserName(true),
        ],
        'position',
    ],
]) ?>
