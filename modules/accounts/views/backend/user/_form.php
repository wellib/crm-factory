<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\modules\accounts\Module;


/* @var $this yii\web\View */
/* @var $model app\modules\accounts\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin([
        'options' => [
            'enctype'=>'multipart/form-data'
        ],
    ]); ?>

    <?= $form->field($model, 'nickname') ?>

    <?= $form->field($model, 'email')->textInput([
        'autocomplete' => 'off',
    ]) ?>

    <?= $form->field($model, 'password')->passwordInput() ?>

    <?php $this->registerCss("
        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 1000px white inset !important;
        }
    "); ?>

		<?php if (!Yii::$app->getUser()->isGuest && Yii::$app->getUser()->getIdentity()->nickname === 'root') : ?>
    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'position') ?>

		<?= $form->field($model, 'rukovodstvo')->checkbox() ?>

		<?= $form->field($model, 'status')->checkbox() ?>
		<?php endif ?>

    <?= $form->field($model, 'roles')->checkboxList($model->getRolesList()) ?>

    <?= $form->field($model, 'avatar_upload')->fileInput() ?>
    <?php if ($avatar = $model->getAvatar()): ?>
        <?= Html::img($avatar) ?>
        <?= $form->field($model, 'avatar_delete')->checkbox() ?>
    <?php endif ?>
    

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Module::t('user', 'CREATE__FORM__SUBMIT_BTN') : Module::t('user', 'UPDATE__FORM__SUBMIT_BTN'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
