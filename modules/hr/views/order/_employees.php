<?php

use yii\web\View;

use app\modules\hr\models\Order;
use app\modules\hr\models\Employee;

use yii\widgets\Menu;

/* @var $this View */
/* @var $model Order */

?>

<?= Menu::widget([
    'options' => ['class' => 'list-unstyled'],
    'items' => array_map(function ($model) {
        /** @var Employee $model */
        return [
            'label' => $model->getFullName(true),
            'url' => $model->getViewUrl(),
        ];
    }, $model->employees),
]) ?>
