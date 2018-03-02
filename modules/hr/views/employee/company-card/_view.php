<?php

use yii\web\View;
use yii\widgets\DetailView;

use app\modules\hr\models\embedded\CompanyCard;

/* @var $this View */
/* @var $model CompanyCard */

?>

<?= DetailView::widget([
    'model' => $model,
    'template' => "<tr><th width=\"225\">{label}</th><td>{value}</td></tr>",
    'attributes' => [
        'employee_id',
        'biometrics_id',
        'contract_number',
        'contract_date',
        'employment_date',
        [
            'attribute' => '_structure_department',
            'format'    => 'raw',
            'value'     => $model->getStructureDepartment(true, true),
        ],
    ],
]) ?>
