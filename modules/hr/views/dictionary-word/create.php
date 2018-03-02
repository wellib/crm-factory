<?php

use yii\helpers\Html;
use app\modules\hr\Module;


/* @var $this yii\web\View */
/* @var $model app\modules\hr\models\DictionaryWord */

$this->title = Module::t('dictionary-word', 'Create Dictionary Word');
$this->params['breadcrumbs'][] = ['label' => Module::t('dictionary-word', 'Dictionary Words'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dictionary-word-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
