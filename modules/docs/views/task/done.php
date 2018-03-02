<?php

use yii\helpers\Html;
use app\modules\docs\models\Task;
use yii\widgets\DetailView;
use app\modules\docs\Module;
use app\themes\gentelella\widgets\Panel;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model Task */

$this->title = $model->subject;
//$this->params['breadcrumbs'][] = ['label' => Module::t('task', 'Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="alert alert-success">
    <h5>Задача "<?= Html::encode($model->subject) ?>" выполнена.</h5>
</div>
