<?php

use app\modules\todo\models\TaskLog;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model TaskLog */
/* @var $task_id mixed */
/* @var $action mixed */

?>

<?php Modal::begin([
    'id' => 'notify_user_disapproved_results-modal',
    'header' => '<h2>Укажите причину</h2>',
]) ?>
<?= $this->render('_comment-form', [
    'model' => $model,
    'task_id' => $task_id,
    'action' => $action,
]) ?>
<?php Modal::end() ?>
