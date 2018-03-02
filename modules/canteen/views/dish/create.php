<?php

use app\modules\canteen\models\DishForm;
use app\themes\gentelella\widgets\Panel;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dishForm DishForm */

$this->title = 'Добавление блюда';
$this->params['breadcrumbs'][] = [
    'label' => 'Блюда',
    'url' => ['index'],
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="canteen-dish-create">

    <?php Panel::begin(); ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'dishForm' => $dishForm,
    ]) ?>

    <?php Panel::end(); ?>

</div>
