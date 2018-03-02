<?php

use yii\helpers\Html;
use app\modules\docs\Module;
use app\themes\gentelella\widgets\Panel;

/* @var $this yii\web\View */
/* @var $model app\modules\docs\models\Task */

$this->title = Module::t('task', 'CREATE__PAGE__TITLE');
//$this->params['breadcrumbs'][] = ['label' => Module::t('task', 'Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Panel::begin(); ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

<?php Panel::end(); ?>
