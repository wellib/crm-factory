<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\modules\docs\Module;
use app\modules\docs\models\Task;
use app\themes\gentelella\widgets\Panel;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\docs\models\TaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('task', 'OUTBOX') . ' / '. Module::t('task', 'OUTBOX_DONE');
$this->params['breadcrumbs'][] = Module::t('task', 'OUTBOX');
$this->params['breadcrumbs'][] = Module::t('task', 'OUTBOX_DONE');
?>
<?php Panel::begin(); ?>

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= $this->render('_outbox-gridview', [
        'searchModel'  => $searchModel,
        'dataProvider' => $dataProvider,
    ]) ?>
    
<?php Panel::end(); ?>
