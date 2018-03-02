<?php

use yii\web\View;
use yii\helpers\Html;

use app\modules\hr\Module;
use app\modules\hr\models\Employee;

use app\themes\gentelella\widgets\Panel;

/* @var $this View */
/* @var $model Employee */

$this->title = Module::t('employee', 'CREATE__PAGE__TITLE');
$this->params['breadcrumbs'][] = ['label' => Module::t('employee', 'MODEL_NAME_PLURAL'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Panel::begin(); ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

<?php Panel::end(); ?>
