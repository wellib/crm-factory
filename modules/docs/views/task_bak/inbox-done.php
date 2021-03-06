<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\modules\docs\Module;
use app\modules\docs\models\Task;
use app\themes\gentelella\widgets\Panel;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\docs\models\TaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $displayStatusColumn null|bool */

$this->title = Module::t('task', 'INBOX') . ' / '. Module::t('task', 'INBOX_DONE');
$this->params['breadcrumbs'][] = Module::t('task', 'INBOX');
$this->params['breadcrumbs'][] = Module::t('task', 'INBOX_DONE');
?>
<?php Panel::begin(); ?>
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= $this->render('_inbox-gridview', [
        'searchModel'  => $searchModel,
        'dataProvider' => $dataProvider,
        'displayStatusColumn' => $displayStatusColumn,
    ]) ?>
<?php Panel::end(); ?>
