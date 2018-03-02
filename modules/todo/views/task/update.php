<?php

use yii\helpers\Html;
use app\modules\todo\Module;
use app\themes\gentelella\widgets\Panel;

/* @var $this yii\web\View */
/* @var $model app\modules\todo\models\Task */

$this->title = Module::t('task', 'UPDATE__PAGE__TITLE') . ': ' .  $model->subject;
//$this->params['breadcrumbs'][] = ['label' => Module::t('task', 'Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->subject, 'url' => ['view', 'id' => (string)$model->_id]];
$this->params['breadcrumbs'][] = Module::t('task', 'UPDATE__PAGE__TITLE');
?>
<?php Panel::begin(); ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

<?php Panel::end(); ?>
