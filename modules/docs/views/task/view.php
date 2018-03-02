<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\docs\Module;
use app\themes\gentelella\widgets\Panel;
use yii\bootstrap\Tabs;
use app\modules\docs\models\Task;

/* @var $this yii\web\View */
/* @var $model Task */

$this->title = $model->subject;
//$this->params['breadcrumbs'][] = ['label' => Module::t('task', 'Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
/*
echo $model->deadline_date.'<br/>';
echo $model->deadline_time.'<br/>';
echo $model->deadline_timestamp.'<br/>';
echo $model->perform_timestamp.'<br/>';
echo $model->perform_date.'<br/>';
echo $model->perform_time.'<br/>';
*/

?>
<?= Tabs::widget([
    'items' => [
        [
            'label' => 'Задача',
            //'content' => 'Anim pariatur cliche...',
            'active' => true
        ],
        [
            'label' => 'Действия',
            //'content' => 'Anim pariatur cliche..123123',
            'url' => ['view2', 'id' => (string) $model->_id]
        ],
        //[
        //    'label' => 'Комментарии',
        //    'url' => ['view3', 'id' => (string) $model->_id]
        //],
    ],

])?>


<div class="task-view">
    <?php Panel::begin(); ?>
    <h1><?= Html::encode($this->title) ?></h1>
    <?php if ((Yii::$app->getUser()->getId() == $model->_author && $model->status !== Task::STATUS__DONE) || Yii::$app->getUser()->getIdentity()->nickname === 'root'): ?>
    <p>
        <?= Html::a(Module::t('task', 'UPDATE___LINK__LABEL'), ['update', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']) ?>
        <? /*= Html::a(Module::t('task', 'DELETE___LINK__LABEL'), ['delete', 'id' => (string)$model->_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Module::t('task', 'DELETE___LINK__CONFIRM_MESSAGE'),
                'method' => 'post',
            ],
        ]) */ ?>
    </p>
    <?php endif; ?>
    <?php foreach ($model->getUserActions() as $action): ?>

        <?php if (isset($action['renderBtn']) && is_callable($action['renderBtn'])): ?>
            <?= $action['renderBtn']($action) ?>
        <?php endif; ?>
        <?php if (isset($action['renderHtml']) && is_callable($action['renderHtml'])): ?>
            <?= $action['renderHtml']($action) ?>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php \yii\redactor\widgets\RedactorAsset::register($this) ?>
    <?= $this->render('_redactor-editor-styles') ?>

<?php
//var_dump($model);

?>
    <?= DetailView::widget([
        'model' => $model,
        'template' => "<tr><th width=\"225\">{label}</th><td>{value}</td></tr>",
        'attributes' => [
            //'_id',
      //'_parent',
            'id',
            'subject',
            [
                'attribute' => 'deadline_type',
                'value' => $model->getDeadlineTypeLabel(),
            ],
            [
                'attribute' => 'deadline_every_week',
                'value' => $model->getDeadlineWeekDays(),
                'visible' => $model->deadline_type === Task::DEADLINE_TYPE__EVERY_WEEK,
            ],
            [
                'attribute' => 'deadline_every_month',
                'value' => implode(', ', $model->deadline_every_month ? $model->deadline_every_month : []),
                'visible' => $model->deadline_type === Task::DEADLINE_TYPE__EVERY_MONTH,
            ],
            //'deadline_approval:datetime',
            [
                'attribute' => 'approve_execute_deadline_timestamp',
                'format' => 'datetime',
                'visible' => count($model->usersApproveExecute) > 0,
            ],
            //'deadline_control_results:datetime',
            [
                'attribute' => 'check_results_deadline_timestamp',
                'format' => 'datetime',
                'visible' => count($model->usersCheckResult) > 0,
            ],
            //'deadline_timestamp:datetime',
            [
                'attribute' => 'perform_timestamp',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'deadline_timestamp',
                'format' => 'datetime',
            ],


            //'last_deadline_timestamp:datetime',



            //'deadline_repeats_number',
            //'deadline_repeats_counter',


            [
                'attribute' => 'priority',
                'format' => 'raw',
                'value' => $model->getPriorityLabel(),
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => $this->render('_status', ['model' => $model]),
            ],
            //'description:raw',
            [
                'attribute' => 'description',
                'format' => 'raw',
                'value' => Html::tag('div', $model->description, [
                    'class' => 'redactor-editor',
                ]),
            ],
            //'_attached_files',
            [
                'attribute' => '_attached_files',
                'format' => 'raw',
                'value' => $this->render('_view-attached-files-list', ['model' => $model]),
            ],
            [
                'attribute' => '_author',
                'format' => 'raw',
                'value' => $this->render('_user-list', ['models' => [$model->author]]),
            ],
            [
                'attribute' => '_users_approve_execute',
                'format' => 'raw',
                'value' => $this->render('_users-list-approve-execute', ['model' => $model]),
                'visible' => count($model->usersApproveExecute) > 0,
            ],
            [
                'attribute' => '_users_performers',
                'format' => 'raw',
                'value' => $this->render('_users-list-performers', ['model' => $model]),
            ],
            //[
            //    'attribute' => '_users_control_execution',
            //    'format' => 'raw',
            //    //'value' => $this->render('_user-list', ['models' => $model->usersControlExecution]),
            //    'value' => $this->render('_users-list-control-execution', ['model' => $model]),
            //],
            [
                'attribute' => '_users_check_result',
                'format' => 'raw',
                //'value' => $this->render('_user-list', ['models' => $model->usersControlResults]),
                'value' => $this->render('_users-list-control-results', ['model' => $model]),
                'visible' => count($model->usersCheckResult) > 0,
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>
    <?php Panel::end(); ?>
</div>
