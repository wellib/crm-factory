<?php

use yii\helpers\Html;
use app\modules\hr\Module;
use app\themes\gentelella\widgets\Panel;


/* @var $this yii\web\View */
/* @var $model app\modules\hr\models\Employee */

$this->title = Module::t('employee', 'UPDATE__PAGE__TITLE');
$this->params['breadcrumbs'][] = ['label' => Module::t('employee', 'MODEL_NAME_PLURAL'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getFullName(), 'url' => $model->getViewUrl()];
$this->params['breadcrumbs'][] = Module::t('employee', 'UPDATE__PAGE__TITLE');
?>
<?php Panel::begin(); ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

<?php Panel::end(); ?>
