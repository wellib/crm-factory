<?php

use yii\web\View;
use yii\web\JsExpression;

use yii\helpers\Url;

use yii\helpers\Html;

use app\modules\structure\Module;
use app\modules\structure\models\Department;

use execut\widget\TreeView;

use app\themes\gentelella\widgets\Panel;


/* @var $this View */

$this->title = Module::t('department', 'MODEL_NAME_PLURAL');
$this->params['breadcrumbs'][] = $this->title;



?>
<?php Panel::begin(); ?>
    <p>
        <?= Html::a(Module::t('department', 'CREATE___LINK__LABEL'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <?= TreeView::widget([
                'data' => Department::treeViewData(),
                'size' => TreeView::SIZE_MIDDLE,
                'header' => $this->title,
                'searchOptions' => [
                    'inputOptions' => [
                        'placeholder' => 'Search category...',
                        'class' => 'hide',
                    ],
                ],
                'clientOptions' => [
                    'enableLinks' => true,
                    'showTags' => true,
                    //'onNodeSelected' => $onSelect,
                    //'selectedBackColor' => 'rgb(40s, 153, 57)',
                    'borderColor' => '#fff',
                ],
            ]) ?>
        </div>
    </div>
<?php Panel::end(); ?>