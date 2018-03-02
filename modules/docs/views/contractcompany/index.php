<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\docs\models\ContractcompanySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Компании';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contractcompany-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
	<ul class="nav nav-tabs">
		<li><?= Html::a('Таблицей', ['/docs/contract/index'], ['class' => '']) ?></li>
		<li><?= Html::a('Деревом', ['/docs/contract/tree'], ['class' => '']) ?></li>
		<li><?= Html::a('Компании', ['/docs/contractcompany/index'], ['class' => '']) ?></li>
	</ul>
<br/>
    <p>
        <?= Html::a('Добавить компанию', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            ['class' => \app\themes\gentelella\widgets\grid\ActionColumn::className()],
        ],
    ]); ?>


</div>
