<?php

use yii\helpers\Html;
use app\modules\todo\models\TaskLog;
use app\themes\gentelella\assets\GentelellaBootstrapThemeAsset;

/** @var $this \yii\web\View */
/** @var $model TaskLog */

$theme = GentelellaBootstrapThemeAsset::register($this);

?>
<li class="alert alert-danger">
    <a>
        <span class="image">
          <img src="<?= $model->user->getAvatar($theme->getUserAvatarDefault()) ?>" alt="img">
        </span>
        <span>
            <span class="time"><?= $model->getCreatedAtFormat() ?></span>
          <span><?= $model->user->getNameAndPosition() ?></span>
            <span class="label label-danger">Не акцептовал выполнение данной задачи.</span>
        </span>
        <span class="message">
            <b>Причина:</b> <?= !empty($model->getComment()) ? $model->getComment() : '<i>Не указана</i>' ?><br>
            <?php if (count($attachedFiles = $model->getAttachedFilesLinks()) > 0): ?>
                <b>Прикрепил файлы:</b>
                <?php $c = 0 ?>
                <?php foreach ($attachedFiles as $url => $filename): ?>
                    <?= Html::tag('span',$filename, [
                        'onclick' => "window.location='" . $url . "'",
                        'style' => 'cursor: pointer;',
                    ]); ?>
                    <?php $c++ ?>
                    <?= $c < count($attachedFiles) ? ', ' : '' ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </span>
    </a>
</li>


