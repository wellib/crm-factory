<?php

use yii\helpers\Html;
use app\modules\accounts\models\User;
use app\modules\todo\models\Task;

/** @var $this \yii\web\View */
/** @var $model Task */

?>
<ul style="padding-left: 15px;margin-bottom: 0;">
<?php foreach ($model->usersPerformers as $user): /** @var User $user */ ?>
    <li><?= Html::a($user->getNameAndPosition(), $user->getViewUrl(),[
        'target' => '_blank',
        'data-pjax' => 0,
    ]) ?>
        <?php if (!in_array($model->status, [Task::STATUS__APPROVAL_AWAITING, Task::STATUS__APPROVAL_FAILED])): ?>
            <?php if ($model->getUserAnswerByAttribute('_users_performers_finished', $user) === true): ?>
                <span class="label label-success">Завершил выполнение задачи</span>
            <?php // if ($model->getUserAnswerByAttribute('_users_performers_execute', $user) === true): ?>
                <!--<span class="label label-primary">Работает над задачей</span>-->
            <?php else: ?>
                <span class="label label-primary">Работает над задачей</span>
                <!--<span class="label label-warning">Ожидаем</span>-->
            <?php endif; ?>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
</ul>