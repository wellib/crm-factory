<?php

use app\modules\docs\models\TaskLog;

/** @var $this \yii\web\View */
/** @var $model TaskLog */

?>

<li class="alert alert-warning">
    <a>
        <span class="image">
            <div style="display: block;float: left;width: 39px;height: 35px;/* text-align: center; */">
                <i class="fa fa-dashcube" style="border: none;font-size: 33px;margin-left: 3px;"></i>
            </div>

        </span>
        <span>
            <span class="time"><?= $model->getCreatedAtFormat() ?></span>
          <span><?= Yii::$app->name ?></span>
        </span>
        <span class="message">
          Задача ожидает повтой акцептации!
        </span>
    </a>
</li>