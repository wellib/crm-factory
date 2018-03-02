<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\accounts\Module;

/* @var $this yii\web\View */
/* @var $model app\modules\accounts\models\User */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Module::t('user', 'MODEL_NAME_PLURAL'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php if (!Yii::$app->getUser()->isGuest && (Yii::$app->getUser()->getIdentity()->nickname === 'root' || (string)$model->_id === Yii::$app->getUser()->getId())): ?>
    <p>
        <?= Html::a(Module::t('user', 'UPDATE___LINK__LABEL'), ['update', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Module::t('user', 'DELETE___LINK__LABEL'), ['delete', 'id' => (string)$model->_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Module::t('user', 'DELETE___LINK__CONFIRM_MESSAGE'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?php endif; ?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'avatar',
                'format' => 'image',
                'value' => $model->getAvatar(),
            ],
            'name',
            'email',
            'position',
        ],
    ]) ?>

</div>
