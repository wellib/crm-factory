<?php

use yii\web\View;
use yii\widgets\DetailView;

use app\modules\hr\models\embedded\IdentityCard;

/* @var $this View */
/* @var $model IdentityCard */

?>

<?= DetailView::widget([
    'model' => $model,
    'template' => "<tr><th width=\"225\">{label}</th><td>{value}</td></tr>",
    'attributes' => [
        'id_number',
        'vat_id',
        'issue_date',
        [
            'attribute' => '_issuing_authority',
            'format'    => 'raw',
            'value'     => $model->getIssuingAuthority(),
        ],
        [
            'attribute' => '_nationality',
            'format'    => 'raw',
            'value'     => $model->getNationality(),
        ],
        'birthplace',
        'registration_address',
        'residential_address',
        [
            'attribute' => '_marital_status',
            'format'    => 'raw',
            'value'     => $model->getMaritalStatus(),
        ],
    ],
]) ?>
