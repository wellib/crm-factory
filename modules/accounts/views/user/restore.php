<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\accounts\Module;

use app\themes\adm_med\ThemeAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\accounts\models\SigninForm */
/* @var $form ActiveForm */

Yii::$app->controller->layout = '@theme/views/layouts/layout.php';

$this->title = 'Восстановление пароля';
$this->params['breadcrumbs'][] = $this->title;


?>


<div>
    <a class="hiddenanchor" id="signup"></a>
    <a class="hiddenanchor" id="signin"></a>

    <div class="login_wrapper">
        <div class="x_panel">
            <div class="x_title" style="border-bottom: 0;">
                <h2><img src="/logo.png"> <span style="color: inherit;"><?= Yii::$app->name ?></span></h2>
                <img src="">
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <br>
                <!--<form id="demo-form2" data-parsley-validate="" class="form-vertical form-label-left" novalidate="">-->

                    <?php $form = ActiveForm::begin(); ?>
                    <?= $form->field($model, 'email') ?>
                    <div class="form-group">
                        <?= Html::submitButton('Восстановить', ['class' => 'btn btn-primary pull-right']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>

                <!--</form>-->
            </div>
        </div>
    </div>
</div>