<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\docs\models\CompanySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Структура';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить компанию', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'_id',
            'name',
            ['class' => \app\themes\gentelella\widgets\grid\ActionColumn::className()],
        ],
    ]); ?>
</div>
