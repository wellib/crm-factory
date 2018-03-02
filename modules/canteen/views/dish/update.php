<?php

use app\modules\canteen\models\DishForm;
use app\themes\gentelella\widgets\Panel;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dishForm DishForm */

$this->title = 'Редактирование блюда';
$this->params['breadcrumbs'][] = [
    'label' => 'Меню',
    'url' => ['index'],
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="canteen-dish-update">

    <?php Panel::begin(); ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'dishForm' => $dishForm,
    ]) ?>

    <?php Panel::end(); ?>

</div>
