<?php

use yii\helpers\Html;
use app\modules\accounts\Module;

use app\themes\gentelella\widgets\Panel;


/* @var $this yii\web\View */
/* @var $model app\modules\accounts\models\User */

$this->title = Module::t('user', 'CREATE__PAGE__TITLE');
$this->params['breadcrumbs'][] = ['label' => Module::t('user', 'MODEL_NAME_PLURAL'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Panel::begin(); ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

<?php Panel::end(); ?>
