<?php

use yii\helpers\Html;
use app\modules\todo\models\Task;
use app\modules\todo\models\TaskSearch;
use app\modules\accounts\models\User;

/** @var $model Task */
/** @var $searchModel TaskSearch */
$searchUser = $searchModel->getUserModel();
$mark = function($model) use ($searchUser) {
    /** @var User $model */
    if ($searchUser && $model->getNameAndPosition() == $searchUser->getNameAndPosition()) {
        return Html::tag('mark', $model->getNameAndPosition());
    } else {
        return $model->getNameAndPosition();
    }
};


?>
<b><?= $model->getAttributeLabel('_author') ?></b>
<ul style="padding-left: 15px;margin-bottom: 5px;">
    <li><?= $mark($model->author) ?></li>
</ul>

<b><?= $model->getAttributeLabel('_users_performers') ?></b>
<ul style="padding-left: 15px;margin-bottom: 5px;">
    <?php foreach ($model->usersPerformers as $user): ?>
        <li><?= $mark($user) ?></li>
    <?php endforeach; ?>
</ul>


<?php if (count($model->usersCheckResult) > 0): ?>
<b><?= $model->getAttributeLabel('_users_check_result') ?></b>
<ul style="padding-left: 15px;margin-bottom: 5px;">
    <?php foreach ($model->usersCheckResult as $user): ?>
        <li><?= $mark($user) ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>
<?php if (count($model->usersApproveExecute) > 0): ?>
<b><?= $model->getAttributeLabel('_users_approve_execute') ?></b>
<ul style="padding-left: 15px;margin-bottom: 5px;">
    <?php foreach ($model->usersApproveExecute as $user): ?>
        <li><?= $mark($user) ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>