<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use app\themes\gentelella\widgets\Panel;
use yii\bootstrap\Tabs;
use app\modules\docs\models\ContractLog;
use yii\bootstrap\Modal;
/* @var $this yii\web\View */
/* @var $model app\modules\docs\models\Contract */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Договора', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$userid = Yii::$app->getUser()->getId();
?>

<?php Modal::begin([
    'id' => 'notify_repeat_accept-modal',
    'header' => '<h2>Дайте комментарий по необходимым доработкам</h2>',
]) ?>
<?= $this->render('log/_comment-form', [
    'model' => new ContractLog(['scenario' => ContractLog::SCENARIO_COMMENT]),
    'contract_id' => $model->getId(),
]) ?>
<?php Modal::end() ?>




<div class="contract-view">

    <h1><?= Html::encode($this->title) ?></h1>

	<?php if ($model->getAccessUpdate()) : ?>
    <p>
        <?= Html::a('Изменить', ['update', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => (string)$model->_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить?',
                'method' => 'post',
            ],
        ]) ?>

		<?php if ($model->_author == $userid) : ?>
        <?= Html::a('Отправить на утверждение', ['sendapprove', 'id' => (string)$model->_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите отправить на утверждение?',
                'method' => 'post',
            ],
        ]) ?>
		<?php endif ;?>

		<?php if ($model->getAccessAccept()) : ?>
        <?= Html::a('Утвердить', ['approve', 'id' => (string)$model->_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите утвердить данный договор?',
                'method' => 'post',
            ],
        ]) ?>
		<?php endif ;?>
		<?php if ($model->getAccessRepeatAccept()) : ?>
        <?= Html::a('Отправить на переработку', '#notify_repeat_accept-modal', [
						'class' => 'btn btn-danger',
						'data-toggle' => 'modal',
						'data-target' => '#notify_repeat_accept-modal'
						]);
				?>
		<?php endif ;?>
    </p>
	<?php endif ;?>
	<?php
			$link_files = '';
			foreach ($model->getAttachedFilesLinks() as $url => $filename) {
				$link_files .= Html::a($filename, $url).'<br/>'; 
			}
	?>

<?= Tabs::widget([
    'items' => [
        [
            'label' => 'Договора',
            'active' => true
        ],
        [
            'label' => 'Комментарии',
            'url' => ['view2', 'id' => (string) $model->_id]
        ],
    ],

])?>

<div class="x_panel">
<div class="x_content">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'number',
            'date',
            [
                    'format' => 'raw',
                    'label' => Yii::t('app', 'Предприятие'),
                    'value' => isset($model->companyName)?$model->companyName:'',
            ],
            'names',
            [
                    'format' => 'raw',
                    'label' => Yii::t('app', 'Привязка'),
                    'value' =>
												'<a target="_blank" href="'. Url::to(['/docs/contract/view', 'id' => (string) $model->parent]).'">'.
												(isset($model->parentContract)?$model->parentContract:'').'</a> '.$model->relatedContractLink,
            ],
            [
                    'format' => 'raw',
                    'label' => Yii::t('app', 'Прикрепенные файлы'),
                    'value' => $link_files,
            ],
            'description',
            [
                    'format' => 'raw',
                    'label' => Yii::t('app', 'Кто добавил'),
                    'value' => isset($model->author)?$model->author:'',
            ],
						'alls',
            'approve:datetime',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>
</div>
</div>

</div>
