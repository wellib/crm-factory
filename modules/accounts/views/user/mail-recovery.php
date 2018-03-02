<?php

use app\modules\accounts\models\RecoveryForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model RecoveryForm */

?>

<p>Восстановить пароль можно по ссылке ниже, если вы не отправляли запрос на восстановление пароля, просто проигнорируйте данное письмо!</p>
<p><?= Html::a('Восстановить пароль', $model->getRecoveryUrl()) ?></p>
