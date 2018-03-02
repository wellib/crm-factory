<?php

use yii\helpers\Html;
use app\modules\accounts\models\User;
use app\modules\todo\models\Task;

/** @var $this \yii\web\View */
/** @var $model Task */

?>
<ul style="padding-left: 15px;margin-bottom: 0;">
<?php foreach ($model->usersApproveExecute as $user): /** @var User $user */ ?>
    <li><?= Html::a($user->getNameAndPosition(), $user->getViewUrl(),[
        'target' => '_blank',
        'data-pjax' => 0,
    ]) ?>
        <?php if ($model->getUserAnswerByAttribute('_users_approve_execute_response', $user) === true): ?>
            <span class="label label-success">Акцептовал</span>
        <?php elseif ($model->getUserAnswerByAttribute('_users_approve_execute_response', $user) === false): ?>
            <span class="label label-danger">Не акцептовал</span>
        <?php else: ?>
            <span class="label label-warning">Ожидаем ответа</span>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
</ul>