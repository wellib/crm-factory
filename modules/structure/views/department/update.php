<?php

use yii\web\View;

use yii\helpers\Html;

use app\modules\structure\Module;
use app\modules\structure\models\Department;

use app\themes\gentelella\widgets\Panel;

/* @var $this View */
/* @var $model Department */

$this->title = Module::t('department', 'UPDATE__PAGE__TITLE');
$this->params['breadcrumbs'][] = ['label' => Module::t('department', 'MODEL_NAME_PLURAL'), 'url' => ['tree']];
//$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->getId(true)]];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php Panel::begin(); ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

<?php Panel::end(); ?>

