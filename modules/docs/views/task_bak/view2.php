<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\docs\Module;
use app\themes\gentelella\widgets\Panel;
use yii\bootstrap\Tabs;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use app\modules\docs\models\TaskLog;

/* @var $this yii\web\View */
/* @var $model app\modules\docs\models\Task */

$this->title = $model->subject;
//$this->params['breadcrumbs'][] = ['label' => Module::t('task', 'Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="pull-right">
    <a href="#" onclick="window.print();return false;" class="btn btn-info"><i class="fa fa-print" aria-hidden="true"></i> Распечатать</a>
</div>
<?= Tabs::widget([
    'items' => [
        [
            'label' => 'Задача',
            //'content' => 'Anim pariatur cliche...',
            'url' => ['view', 'id' => (string) $model->_id]

        ],
        [
            'label' => 'Действия',
            //'content' => 'Anim pariatur cliche..123123',
            'active' => true
        ],
        //[
        //    'label' => 'Комментарии',
        //    'url' => ['view3', 'id' => (string) $model->_id]
        //],
    ],

])?>



<?php Panel::begin(); ?>
<ul class="list-unstyled msg_list chat-log" style="">
    <?php foreach(\app\modules\docs\models\TaskLog::find()->where(['task_id' => $model->_id])->orderBy(['_id' => SORT_ASC])->all() as $log): /** @var $log \app\modules\docs\models\TaskLog */ ?>
        <?= $this->render('log/'.$log->type, ['model' => $log]) ?>
    <?php endforeach; ?>
</ul>
<?php Pjax::begin([
    'enablePushState' => false,
]) ?>
<div class="print-hide">
<?= $this->render('log/_comment-form', [
    'model' => new TaskLog(['scenario' => TaskLog::SCENARIO_COMMENT]),
    'task_id' => $model->getId(),
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
