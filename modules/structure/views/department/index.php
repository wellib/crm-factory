<?php

use yii\web\View;

use yii\helpers\Html;

use yii\grid\GridView;

use yii\data\ActiveDataProvider;

use app\modules\structure\Module;
use app\modules\structure\models\DepartmentSearch;

/* @var $this View */
/* @var $searchModel DepartmentSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Module::t('department', 'MODEL_NAME_PLURAL');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="department-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Module::t('department', 'CREATE___LINK__LABEL'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            '_id',
            'name',
            'icon',
            '_parent',
            //'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
