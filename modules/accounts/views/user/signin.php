<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\accounts\Module;

use app\themes\adm_med\ThemeAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\accounts\models\SigninForm */
/* @var $form ActiveForm */

Yii::$app->controller->layout = '@theme/views/layouts/layout.php';

$this->title = Module::t('signin', 'PAGE_TITLE');
$this->params['breadcrumbs'][] = $this->title;


?>


<div>
    <a class="hiddenanchor" id="signup"></a>
    <a class="hiddenanchor" id="signin"></a>

    <div class="login_wrapper">
        <div class="x_panel">
            <div class="x_title text-center" style="border-bottom: 0;">
                <h2 style="margin: 7px 0 6px 63px;"><img src="/logo.png"> <span style="color: inherit;"><?= Yii::$app->name ?></span></h2>
                <img src="">
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <br>
                <!--<form id="demo-form2" data-parsley-validate="" class="form-vertical form-label-left" novalidate="">-->

                    <?php $form = ActiveForm::begin(); ?>
                    <?= $form->field($model, 'login') ?>
                    <?= $form->field($model, 'password')->passwordInput() ?>
                    <?= $form->field($model, 'rememberMe')->checkbox() ?>
                    <div class="form-group">
                        <?= Html::a('Восстановить пароль', ['recovery'], ['class' => 'btn btn-link pull-left']) ?>
                        <?= Html::submitButton(Module::t('signin', 'FORM_SUBMIT_BTN'), ['class' => 'btn btn-primary pull-right']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>

                <!--</form>-->
            </div>
        </div>
    </div>
</div>