<?php

use yii\web\View;

use yii\helpers\Html;
use yii\widgets\DetailView;

use app\modules\hr\Module;
use app\modules\hr\models\Order;

use app\themes\gentelella\widgets\Panel;

/* @var $this View */
/* @var $model Order */

$this->title = $model->getTitle();
$this->params['breadcrumbs'][] = ['label' => Module::t('order', 'MODEL_NAME_PLURAL'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Panel::begin(); ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Module::t('order', 'UPDATE___LINK__LABEL'), ['update', 'id' => $model->getId(true)], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Module::t('order', 'DELETE___LINK__LABEL'), ['delete', 'id' => $model->getId(true)], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Module::t('order', 'DELETE___LINK__CONFIRM_MESSAGE'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'template' => "<tr><th width=\"250\">{label}</th><td>{value}</td></tr>",
        'attributes' => [
            [
                'attribute' => 'type',
                'value' => $model->getTypeLabel(),
            ],
            [
                'attribute' => '_employees',
                'format' => 'raw',
                'value' => $this->render('_employees', ['model' => $model]),
            ],
            'number',
            'date',
            'note',
        ],
    ]) ?>

    <?= $this->render($model->getEmbeddedModelViewDir() . '/_view', [
        'owner' => $model,
        'model' => $model->getEmbeddedModelByType(),
    ]) ?>


<?php Panel::end(); ?>

