<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\hr\Module;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('dictionary-word', 'Dictionary Words');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dictionary-word-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Module::t('dictionary-word', 'Create Dictionary Word'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            '_id',
            'dictionary',
            'word',
            'created_at',
            'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
