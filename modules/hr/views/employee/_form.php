<?php

use yii\web\View;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

use app\modules\hr\Module;
use app\modules\hr\models\Employee;
use app\themes\gentelella\widgets\Collapse;

/* @var $this View */
/* @var $model Employee */
/* @var $form ActiveForm */
?>

<div class="employee-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= Collapse::widget([
        'autoCollapsingPreviously' => false,
        'items' => [
            [
                'label' => Module::t('employee', 'ACCORDION_PANEL_HEADER_TITLE'),
                'content' => $this->render('main-info/_form', [
                    'form'  => $form,
                    'model' => $model,
                ]),
                // open its content by default
                'contentOptions' => ['class' => 'in']
            ],
            [
                'label' => Module::t('identity-card', 'ACCORDION_PANEL_HEADER_TITLE'),
                'content' => $this->render('identity-card/_form', [
                    'form'  => $form,
                    'model' => $model->identityCard,
                ]),
            ],
            [
                'label' => Module::t('company-card', 'ACCORDION_PANEL_HEADER_TITLE'),
                'content' => $this->render('company-card/_form', [
                    'form'  => $form,
                    'model' => $model->companyCard,
                ]),
            ],
            [
                'label' => Module::t('contact', 'ACCORDION_PANEL_HEADER_TITLE'),
                'options' => ['id' => 'accordion-section-contacts'],
                'content' => $this->render('contacts/_form', [
                    'model' => $model,
                    'form'  => $form,
                ]),
            ],
            [
                'label' => Module::t('education', 'ACCORDION_PANEL_HEADER_TITLE'),
                'options' => ['id' => 'accordion-section-education'],
                'content' => $this->render('education/_form', [
                    'model' => $model,
                    'form'  => $form,
                ]),
            ],
            [
                'label' => Module::t('family', 'ACCORDION_PANEL_HEADER_TITLE'),
                'options' => ['id' => 'accordion-section-family'],
                'content' => $this->render('family/_form', [
                    'model' => $model,
                    'form'  => $form,
                ]),
            ],
            [
                'label' => Module::t('experience', 'ACCORDION_PANEL_HEADER_TITLE'),
                'options' => ['id' => 'accordion-section-experience'],
                'content' => $this->render('experience/_form', [
                    'model' => $model,
                    'form'  => $form,
                ]),
            ],
            [
                'label' => Module::t('file', 'ACCORDION_PANEL_HEADER_TITLE'),
                'options' => ['id' => 'accordion-section-file'],
                'content' => $this->render('file/_form', [
                    'model' => $model,
                    'form'  => $form,
                ]),
            ],
        ]
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Module::t('employee', 'Create') : Module::t('employee', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?= \app\modules\hr\widgets\DictionaryWordCrudWidget::widget() ?>
</div>
