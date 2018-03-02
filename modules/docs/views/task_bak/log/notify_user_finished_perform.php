<?php

use yii\helpers\Html;
use app\modules\docs\models\TaskLog;
use app\themes\gentelella\assets\GentelellaBootstrapThemeAsset;

/** @var $this \yii\web\View */
/** @var $model TaskLog */

$theme = GentelellaBootstrapThemeAsset::register($this);

?>

<li class="alert alert-info">
    <a>
        <span class="image">
          <img src="<?= $model->user->getAvatar($theme->getUserAvatarDefault()) ?>" alt="img">
        </span>
        <span>
            <span class="time"><?= $model->getCreatedAtFormat() ?></span>
          <span><?= $model->user->getNameAndPosition() ?></span>
            <span class="label label-info">Завершил(а) выполнение задачи</span>
        </span>
        <span class="message">
            <b>Комментарий:</b> <?= !empty($model->getComment()) ? $model->getComment() : '<i>Не указан</i>' ?><br>
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


