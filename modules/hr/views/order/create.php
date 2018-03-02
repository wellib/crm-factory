<?php

use yii\web\View;

use yii\helpers\Html;

use app\modules\hr\Module;
use app\modules\hr\models\Order;

use app\themes\gentelella\widgets\Panel;

/* @var $this View */
/* @var $model Order */

$this->title = Module::t('order', 'CREATE__PAGE__TITLE') . ' - ' . $model->getTypeLabel();
$this->params['breadcrumbs'][] = ['label' => Module::t('order', 'MODEL_NAME_PLURAL'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Panel::begin(); ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

<?php Panel::end(); ?>
