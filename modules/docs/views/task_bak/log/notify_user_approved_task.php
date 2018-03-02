<?php

use app\modules\docs\models\TaskLog;
use app\themes\gentelella\assets\GentelellaBootstrapThemeAsset;

/** @var $this \yii\web\View */
/** @var $model TaskLog */

$theme = GentelellaBootstrapThemeAsset::register($this);

?>
<li class="alert alert-success">
    <a>
        <span class="image">
          <img src="<?= $model->user->getAvatar($theme->getUserAvatarDefault()) ?>" alt="img">
        </span>
        <span>
            <span class="time"><?= $model->getCreatedAtFormat() ?></span>
          <span><?= $model->user->getNameAndPosition() ?></span>
        </span>
        <span class="message">
          Акцептовал выполнение данной задачи.
        </span>
    </a>
</li>
