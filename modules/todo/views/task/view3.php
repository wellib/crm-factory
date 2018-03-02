<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\todo\Module;
use app\themes\gentelella\widgets\Panel;
use yii\bootstrap\Tabs;
use yii\widgets\ActiveForm;
use app\themes\gentelella\assets\GentelellaBootstrapThemeAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\todo\models\Task */

$theme = GentelellaBootstrapThemeAsset::register($this);

$this->title = $model->subject;
//$this->params['breadcrumbs'][] = ['label' => Module::t('task', 'Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= Tabs::widget([
    'items' => [
        [
            'label' => 'Задача',
            'content' => 'Anim pariatur cliche...',
            'url' => ['view', 'id' => (string) $model->_id]

        ],
        [
            'label' => 'Действия',
            'url' => ['view2', 'id' => (string) $model->_id]

        ],
        //[
        //    'label' => 'Комментарии',
        //    'active' => true
        //],
    ],

])?>
<?php Panel::begin(); ?>
<?php $form = ActiveForm::begin(); ?>
<?= $form->field($comment, 'comment')->textarea() ?>
<div class="form-group">
    <?= Html::submitButton('Отправить комментарий', [
        'class' => $comment->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
    ]) ?>
</div>
<?php ActiveForm::end(); ?>
<ul class="list-unstyled msg_list">
    <?php foreach ($comments as $comm): /** @var $comm \app\modules\todo\models\TaskComment */ ?>
        <li>
            <a>
                        <span class="image">
                          <img src="<?= $comm->user->getAvatar($theme->getUserAvatarDefault()) ?>" alt="img">
                        </span>
                        <span>
                          <span><?= $comm->user->getNameAndPosition() ?></span>
                          <!--<span class="time">3 mins ago</span>-->
                        </span>
                        <span class="message">
                          <?= $comm->getText() ?>
                        </span>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
<?php Panel::end(); ?>

