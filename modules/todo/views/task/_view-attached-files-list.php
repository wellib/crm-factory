<?php

use yii\helpers\Html;
use app\modules\todo\models\Task;

/** @var $this \yii\web\View */
/** @var $model Task */

?>

<ul style="padding-left: 15px;margin-bottom: 0;">
    <?php foreach ($model->getAttachedFilesLinks() as $url => $filename): ?>
        <li><?= Html::a($filename, $url); ?></li>
    <?php endforeach; ?>
</ul>
