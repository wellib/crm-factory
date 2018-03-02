<?php

use yii\helpers\Html;
use app\modules\accounts\models\User;
use app\modules\docs\models\Task;

/** @var $this \yii\web\View */
/** @var $model Task */

?>
<ul style="padding-left: 15px;margin-bottom: 0;">
<?php foreach ($model->usersCheckResult as $user): /** @var User $user */ ?>
    <li><?= Html::a($user->getNameAndPosition(), $user->getViewUrl(),[
        'target' => '_blank',
        'data-pjax' => 0,
    ]) ?>
        <?php if (in_array($model->status, [Task::STATUS__CHECK_RESULTS_AWAITING, Task::STATUS__CHECK_RESULTS_FAILED, Task::STATUS__DONE])): ?>
            <?php if ($model->getUserAnswerByAttribute('_users_check_result_response', $user) === true): ?>
                <span class="label label-success">Акцептовал результат</span>
            <?php elseif ($model->getUserAnswerByAttribute('_users_check_result_response', $user) === false): ?>
                <span class="label label-danger">Не акцептовал результат</span>
            <?php else: ?>
                <span class="label label-warning">Ожидаем ответа</span>
            <?php endif; ?>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
</ul>