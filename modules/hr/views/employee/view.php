<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\DetailView;

use app\modules\hr\Module;
use app\modules\hr\models\Employee;

use app\themes\gentelella\widgets\Panel;
use app\themes\gentelella\widgets\Collapse;


/* @var $this View */
/* @var $model Employee */

$this->title = $model->getFullName();
$this->params['breadcrumbs'][] = ['label' => Module::t('employee', 'MODEL_NAME_PLURAL'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Panel::begin(); ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Module::t('employee', 'UPDATE___LINK__LABEL'), ['update', 'id' => $model->getId(true)], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Module::t('employee', 'DELETE___LINK__LABEL'), ['delete', 'id' => $model->getId(true)], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Module::t('employee', 'DELETE___LINK__CONFIRM_MESSAGE'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= Collapse::widget([
        'autoCollapsingPreviously' => false,
        'items' => [
            [
                'label' => Module::t('employee', 'ACCORDION_PANEL_HEADER_TITLE'),
                'content' => $this->render('main-info/_view', [
                    'model' => $model,
                ]),
                // open its content by default
                'contentOptions' => ['class' => 'in']
            ],
            [
                'label' => Module::t('identity-card', 'ACCORDION_PANEL_HEADER_TITLE'),
                'content' => $this->render('identity-card/_view', [
                    'model' => $model->identityCard,
                ]),
            ],
            [
                'label' => Module::t('company-card', 'ACCORDION_PANEL_HEADER_TITLE'),
                'content' => $this->render('company-card/_view', [
                    'model' => $model->companyCard,
                ]),
            ],
            [
                'label' => Module::t('contact', 'ACCORDION_PANEL_HEADER_TITLE'),
                'content' => $this->render('contacts/_view', [
                    'models' => (array) $model->contacts,
                ]),
            ],
            [
                'label' => Module::t('education', 'ACCORDION_PANEL_HEADER_TITLE'),
                'content' => $this->render('education/_view', [
                    'models' => (array) $model->educations,
                ]),
            ],
            [
                'label' => Module::t('family', 'ACCORDION_PANEL_HEADER_TITLE'),
                'content' => $this->render('family/_view', [
                    'models' => (array) $model->family,
                ]),
            ],
            [
                'label' => Module::t('experience', 'ACCORDION_PANEL_HEADER_TITLE'),
                'content' => $this->render('experience/_view', [
                    'models' => (array) $model->experience,
                ]),
            ],
            [
                'label' => Module::t('file', 'ACCORDION_PANEL_HEADER_TITLE'),
                'content' => $this->render('file/_view', [
                    'models' => $model->files,
                ]),
            ],
        ]
    ]) ?>

<?php Panel::end(); ?>
