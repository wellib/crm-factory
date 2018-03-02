<?php

use yii\helpers\Html;
use app\modules\accounts\models\User;
use app\modules\docs\models\Task;

/** @var $this \yii\web\View */
/** @var $model Task */

?>
<ul style="padding-left: 15px;margin-bottom: 0;">
<?php foreach ($model->usersControlExecution as $user): /** @var User $user */ ?>
    <li><?= Html::a($user->getNameAndPosition(), $user->getViewUrl(),[
        'target' => '_blank',
        'data-pjax' => 0,
    ]) ?>
        <?php if (in_array($model->status, [Task::STATUS_IN_PROGRESS, Task::STATUS_AWAITING_CHECK_RESULTS, Task::STATUS_DONE, Task::STATUS_DISAPPROVE_RESULTS,])): ?>
            <?php if ($model->getUserAnswerByAttribute('_users_approved_finished_perform', $user) === true): ?>
                <span class="label label-success">Завершил контроль</span>
            <?php else: ?>
                <span class="label label-warning">Контролирует выполнение</span>
            <?php endif; ?>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
</ul>
