<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\modules\docs\Module;
use app\modules\docs\models\Task;
use app\themes\gentelella\widgets\Panel;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\docs\models\TaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('task', 'OUTBOX');
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = Module::t('task', 'AWAITING_APPROVAL');
?>
<?php Panel::begin(); ?>

    <h1><?= Html::encode(Module::t('task', 'OUTBOX') . ' / '. Module::t('task', 'AWAITING_APPROVAL')) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?php Pjax::begin(); ?>
    <div class="table-responsive">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'deadline:date',
            //'_id',
            'subject',
            'priority',
            //'description',
            // '_attached_files',
            // '_author',
            // '_users_performers',
            [
                'attribute' => '_users_performers',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var Task $model */
                    return $this->render('_user-list', ['models' => $model->usersPerformers]);
                },
            ],
            //[
            //    'attribute' => '_users_control_execution',
            //    'format' => 'raw',
            //    'value' => function($model) {
            //        /** @var Task $model */
            //        return $this->render('_user-list', ['models' => $model->usersControlExecution]);
            //    },
            //],
            //[
            //    'attribute' => '_users_control_results',
            //    'format' => 'raw',
            //    'value' => function($model) {
            //        /** @var Task $model */
            //        return $this->render('_user-list', ['models' => $model->usersControlResults]);
            //    },
            //],
            [
                'attribute' => '_users_approve_execute',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var Task $model */
                    return $this->render('_user-list', ['models' => $model->usersApproveExecute]);
                },
            ],
            // '_users_control_execution',
            // '_users_control_results',
            // '_users_notify_after_finishing',
            // '_users_approve',
            // 'created_at',
            // 'updated_at',

            ['class' => \app\themes\gentelella\widgets\grid\ActionColumn::className()],
        ],
    ]); ?>
    </div>
<?php Pjax::end(); ?>
</div>
<?php Panel::end(); ?>
