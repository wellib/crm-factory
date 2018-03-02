<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\modules\todo\Module;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\todo\models\TaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('task', 'Tasks');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Module::t('task', 'Create Task'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            '_id',
            'subject',
            'deadline:date',
            'priority',
            'description',
            // '_attached_files',
            // '_author',
            // '_users_performers',
            // '_users_control_execution',
            // '_users_control_results',
            // '_users_notify_after_finishing',
            // '_users_approve',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
