<?php

use app\modules\todo\models\TaskLog;
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
          Изменил задачу
            <table class="table table-border" style="max-width: 800px;">
                <thead>
                    <tr>
                        <th width="20"></th>
                        <th width="40">Было</th>
                        <th width="40">Изменил на</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($model->getDiffTaskModelDataAfterUpdate() as $row): ?>
                <tr>
                    <th><?= $row['label'] ?></th>
                    <td><?= $row['oldValue'] ?></td>
                    <td><?= $row['newValue'] ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

        </span>
    </a>
</li>
