<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
//use yii\grid\GridView;

use app\modules\hr\Module;
use app\modules\hr\models\Employee;
use app\modules\hr\models\embedded\Contact;

use kartik\grid\GridView;
use kartik\export\ExportMenu;

use app\themes\gentelella\widgets\Panel;
use app\themes\gentelella\widgets\grid\ActionColumn;


/* @var $this yii\web\View */
/* @var $searchModel app\modules\hr\models\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('employee', 'MODEL_NAME_PLURAL');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Panel::begin(); ?>

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Module::t('employee', 'CREATE___LINK__LABEL'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => call_user_func(function () {
                /** @var Employee\ $model */
                $columns = Employee::exportColumnsConfig();
                foreach (Employee::getEmbeddedModelsConfig() as $attribute => $className) {
                    if (method_exists($className, 'exportColumnsConfig')) {
                        $columns = ArrayHelper::merge(
                            $columns,
                            $className::exportColumnsConfig($attribute)
                        );
                    }
                }
                return $columns;
            }),
            'target' => ExportMenu::TARGET_SELF,
            'showConfirmAlert' => false,
            'exportConfig' => [
                ExportMenu::FORMAT_TEXT => false,
                ExportMenu::FORMAT_PDF => false,
                ExportMenu::FORMAT_HTML => false,
                ExportMenu::FORMAT_CSV => false,
            ],
        ]); ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'full_name',
                'label' => $searchModel->getAttributeLabel('full_name'),
                //'filter' => false,
                'value' => function($model) {
                    /** @var Employee $model */
                    return $model->getFullName(true);
                }
            ],
            [
                'attribute' => 'structure_department',
                'label' => \app\modules\structure\Module::t('department', 'MODEL_NAME'),
                'filter' => \app\modules\structure\widgets\DepartmentInputWidget::widget([
                    'model' => $searchModel,
                    'attribute' => 'structure_department',
                ]),
                'format' => 'raw',
                'value' => function($model) {
                    /** @var Employee $model */
                    if ($model->companyCard) {
                        return $model->companyCard->getStructureDepartment(true, true);
                    }
                    return null;
                }
            ],
            [
                'attribute' => 'position',
                'label' => $searchModel->getAttributeLabel('position'),
                //'filter' => false,
                'value' => function($model) {
                    /** @var Employee $model */
                    return $model->position;
                }
            ],
            [
                'attribute' => 'contacts',
                'label' => $searchModel->getAttributeLabel('contacts'),
                //'filter' => false,
                'format' => 'raw',
                'value' => function($model) {
                    /** @var Employee $model */
                    return \yii\widgets\Menu::widget([
                        'items' => array_map(function($contact) {
                            /** @var Contact $contact */
                            return [
                                'label' => implode(': ', [
                                    $contact->getType(),
                                    $contact->value,
                                ]),
                            ];
                        }, $model->getMainContacts()),
                    ]);
                }
            ],

            [
                'attribute' => 'employee_id',
                'label' => $searchModel->getAttributeLabel('employee_id'),
                //'filter' => false,
                'value' => function($model) {
                    /** @var Employee $model */
                    if ($model->companyCard) {
                        return $model->companyCard->employee_id;
                    }
                    return null;
                }
            ],

            [
                'class' => ActionColumn::className()
            ],
        ],
    ]); ?>
<?php Panel::end(); ?>
