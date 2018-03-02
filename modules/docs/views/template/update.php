<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\docs\models\Template */

$this->title = 'Изменение';
$this->params['breadcrumbs'][] = ['label' => 'Шаблоны документов', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => (string)$model->_id]];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="template-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
