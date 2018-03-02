<?php

use yii\helpers\Html;

/** @var $models \app\modules\accounts\models\User[] */

?>
<ul style="padding-left: 15px;margin-bottom: 0;">
<?php foreach ($models as $model): ?>
    <li><?= Html::a($model->getNameAndPosition(), $model->getViewUrl(),[
        'target' => '_blank',
        'data-pjax' => 0,
    ]) ?></li>
<?php endforeach; ?>
</ul>