<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\docs\models\Contract */

$this->title = 'Реестр договоров - Изменение';
$this->params['breadcrumbs'][] = ['label' => 'Договора', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contract-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
