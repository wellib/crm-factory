<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\accounts\Module;

use app\modules\accounts\models\RecoveryForm;
//use app\themes\adm_med\ThemeAsset;

/* @var $this yii\web\View */
/* @var $model RecoveryForm */
/* @var $form ActiveForm */

Yii::$app->controller->layout = '@theme/views/layouts/layout.php';

$this->title = Module::t('recovery', 'PAGE_TITLE');
$this->params['breadcrumbs'][] = $this->title;


?>


<div>
    <!--<a class="hiddenanchor" id="signup"></a>-->
    <!--<a class="hiddenanchor" id="signin"></a>-->

    <div class="login_wrapper" style="max-width: 450px;">
        <div class="x_panel">
            <div class="x_title" style="border-bottom: 0;">
                <h2><img src="/logo.png"> <span style="color: inherit;"><?= Yii::$app->name ?></span> / <?= Html::encode($this->title) ?></h2>
                <!--<img src="">-->
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php if(Yii::$app->session->hasFlash(RecoveryForm::FLASH_KEY__SUCCESSFULLY)): ?>
                    <div class="alert alert-success">
                        На e-mail адрес учетной записи отправлено письмо с инструкциями по восстановлению пароля!
                    </div>
                <?php endif; ?>
                <!--<br>-->
                <!--<form id="demo-form2" data-parsley-validate="" class="form-vertical form-label-left" novalidate="">-->
                    <?php $form = ActiveForm::begin(); ?>
                    <?= $form->field($model, 'login') ?>
                    <div class="form-group">
                        <?= Html::a('Вернуться на страницу входа', ['signin'], ['class' => 'btn btn-link pull-left']) ?>
                        <?= Html::submitButton(Module::t('recovery', 'FORM_SUBMIT_BTN'), ['class' => 'btn btn-danger pull-right']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                <!--</form>-->
            </div>
        </div>
    </div>
</div>