<?php

use yii\web\View;

use yii\helpers\Html;
use yii\widgets\DetailView;

use app\modules\structure\Module;
use app\modules\structure\models\Department;

use app\themes\gentelella\widgets\Panel;

/* @var $this View */
/* @var $model Department */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Module::t('department', 'MODEL_NAME_PLURAL'), 'url' => ['tree']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Panel::begin(); ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Module::t('department', 'UPDATE___LINK__LABEL'), ['update', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Module::t('department', 'DELETE___LINK__LABEL'), ['delete', 'id' => (string)$model->_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Module::t('department', 'DELETE___LINK__CONFIRM_MESSAGE'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'template' => "<tr><th width=\"250\">{label}</th><td>{value}</td></tr>",
        'attributes' => [
            'name',
            [
                'attribute' => 'icon',
                'format' => 'raw',
                'value' => !empty($model->icon) ? Html::tag('i', '', ['class' => $model->icon]) : null,
            ],
            [
                'attribute' => '_parent',
                'format' => 'raw',
                'value' => $model->parent ? Html::a(Html::tag('i', '', ['class' => $model->parent->icon]) . ' ' . $model->parent->name, ['view', 'id' => $model->parent->getId(true)]) : null,
            ]
            //'created_at',
            //'updated_at',
        ],
    ]) ?>

<?php Panel::end(); ?>
