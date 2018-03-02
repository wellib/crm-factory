<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use app\themes\gentelella\widgets\Panel;
use yii\bootstrap\Tabs;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
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
            'url' => ['view', 'id' => (string) $model->_id]
        ],
        [
            'label' => 'Комментарии',
						'active' => true
            
        ],
    ],

])?>



<?php Panel::begin(); ?>
<ul class="list-unstyled msg_list chat-log" style="">
    <?php foreach(\app\modules\docs\models\ContractLog::find()->where(['contract_id' => $model->_id])->orderBy(['_id' => SORT_ASC])->all() as $log):  ?>
        <?= $this->render('log/'.$log->type, ['model' => $log]) ?>
    <?php endforeach; ?>
</ul>
<?php Pjax::begin([
    'enablePushState' => false,
]) ?>
<div class="print-hide">
<?= $this->render('log/_comment-form', [
    'model' => new ContractLog(['scenario' => ContractLog::SCENARIO_COMMENT]),
    'contract_id' => $model->getId(),
]) ?>
</div>
<?php Pjax::end(); ?>
<?php $this->registerJs("

reloadChatTimeout = undefined;
reloadChat = function(){

    if (typeof reloadChatTimeout !== 'undefined') {
        clearTimeout(reloadChatTimeout);
    }
    $.ajax({
        url: '" . \yii\helpers\Url::to(['next-comments']) . "',
         type: 'get',
         data: {
            id: '" . (string)$model->_id . "',
            total: $('.msg_list > li').length
         },
         success: function(data){
            $('.msg_list').append(data);
            scrollChat(false);
            //.animate({ scrollTop: $('.msg_list')[0].scrollHeight }, 'fast');
          reloadChatTimeout =  setTimeout(function(){
            reloadChat();
         }, 1000);
         }
         
    });
};

scrollChat = function(scolled) {
    //console.log($('.msg_list').scrollTop() + 50 === $('.msg_list')[0].scrollHeight);
    //console.log($('.msg_list').scrollTop() + 50, $('.msg_list')[0].scrollHeight);
    console.log( $('.msg_list').scrollTop() + 100, $('.msg_list')[0].scrollHeight - 300);
    if (scolled === true || $('.msg_list').scrollTop() + 100 > $('.msg_list')[0].scrollHeight - 300) {
        $('.msg_list').animate({ scrollTop: $('.msg_list')[0].scrollHeight }, 'fast');
    }
};
scrollChat(true);
reloadChat();
//
//form.on('submit', function(e){
//    
//    $.ajax({
//        url: form.attr('action'),
//         type: 'post',
//         data: form.serialize(),
//         success: function(){
//            form[0].reset();
//            //reloadChat();
//         }
//    });
//    return false;
//});
", \yii\web\View::POS_READY) ?>

<?php Panel::end(); ?>

<?php $this->registerCss("
ul.msg_list li:last-child {
         padding: 5px;
}
ul.msg_list li {
    width: 100% !important;
        margin-left: 0;
}
ul.msg_list li a {
    padding: 3px 5px !important;
    display: block;
    width: 100%;
}
ul.msg_list li a .image img {
    border-radius: 2px 2px 2px 2px;
    -webkit-border-radius: 2px 2px 2px 2px;
    float: left;
    margin-right: 10px;
    width: 30px;
}
ul.msg_list li a .time {
        position: relative;
    float: right;
}
.chat-log {
    height: 300px;overflow-y: auto;
}
"); ?>

<?php $this->registerCss("

@media print {
   .print-hide {
        display:none;
   }
   .chat-log {
     height: auto;
   }
   .breadcrumb, .nav_menu {
    display:none;
   }
   .nav.nav-tabs {
   display:none;
   }
   
}

") ?>

</div>
