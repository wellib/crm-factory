<?php

use yii\helpers\Html;
use app\modules\hr\Module;

/* @var $this yii\web\View */
/* @var $model app\modules\hr\models\DictionaryWord */

$this->title = Module::t('dictionary-word', 'Update {modelClass}: ', [
    'modelClass' => 'Dictionary Word',
]) . $model->_id;
$this->params['breadcrumbs'][] = ['label' => Module::t('dictionary-word', 'Dictionary Words'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->_id, 'url' => ['view', 'id' => (string)$model->_id]];
$this->params['breadcrumbs'][] = Module::t('dictionary-word', 'Update');
?>
<div class="dictionary-word-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
