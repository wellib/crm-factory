<?php

use yii\helpers\Html;
use app\modules\docs\models\Task;

/** @var $this \yii\web\View */
/** @var $model Task */


$className = '';

?>
<?= Html::tag('span', $model->getStatusLabel(), [
    'class' => '',
]); ?>
