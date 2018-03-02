<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\modules\accounts\Module;
use app\themes\gentelella\assets\GentelellaBootstrapThemeAsset as Theme;


/* @var $this yii\web\View */
/* @var $searchModel app\modules\accounts\models\backend\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('user', 'MODEL_NAME_PLURAL');
$this->params['breadcrumbs'][] = $this->title;

$theme = Theme::register($this);


?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Module::t('user', 'CREATE___LINK__LABEL'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'avatar',
                'filter' => false,
                'format' => 'raw',
                'value' => function($model) use ($theme) {
                    /** @var \app\modules\accounts\models\User $model */
                    return Html::img($model->getAvatar($theme->getUserAvatarDefault()),[
                        'class' => 'img-circle',
                        'width' => 100,
                    ]);
                },
            ],
            'nickname',
            'name',
            //'email',
            'position',
            ['class' => \app\themes\gentelella\widgets\grid\ActionColumn::className()],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
