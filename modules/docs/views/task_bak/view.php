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
    <?php if (Yii::$app->getUser()->getId() == $model->_author && $model->status !== Task::STATUS__DONE): ?>
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

    <?php if ($model->scenario == Task::SCENARIO_INBOX): ?>

        <?= DetailView::widget([
            'model' => $model,
            'template' => "<tr><th width=\"225\">{label}</th><td>{value}</td></tr>",
            'attributes' => [
                //'_id',
                'id',
                'doc_no',
                'date',
                '_company',
                'subject',
                'doc_from',
                '_based_on',
                'inbox_status',

                'created_at:datetime',
                'updated_at:datetime',
            ],
        ]) ?>

    <?php endif; ?>

    <?php if ($model->scenario == Task::SCENARIO_OUTBOX): ?>

        <?= DetailView::widget([
            'model' => $model,
            'template' => "<tr><th width=\"225\">{label}</th><td>{value}</td></tr>",
            'attributes' => [
                //'_id',
                'id',
                'doc_no',
                'date',
                '_company',
                'subject',
                'doc_from',
                '_based_on',
                'inbox_status',

                'created_at:datetime',
                'updated_at:datetime',
            ],
        ]) ?>

    <?php endif; ?>


    <?php Panel::end(); ?>
</div>
