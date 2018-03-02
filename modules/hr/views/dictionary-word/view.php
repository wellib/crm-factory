<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\hr\Module;

/* @var $this yii\web\View */
/* @var $model app\modules\hr\models\DictionaryWord */

$this->title = $model->_id;
$this->params['breadcrumbs'][] = ['label' => Module::t('dictionary-word', 'Dictionary Words'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dictionary-word-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Module::t('dictionary-word', 'Update'), ['update', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Module::t('dictionary-word', 'Delete'), ['delete', 'id' => (string)$model->_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Module::t('dictionary-word', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            '_id',
            'dictionary',
            'word',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
