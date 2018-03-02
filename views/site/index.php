<?php

/* @var $this yii\web\View */

$this->title = 'ЭКО-СИСТЕМА';
?>
<br>
<?php
$name = 'посетитель';
if (($user = Yii::$app->getUser()->getIdentity()) !== null) {
$name = $user->name;
}
?>
<div class="site-index">
    <div class="jumbotron">
        <h1>Уважаемый, <?= $name ?>!<br/>Добро пожаловать на ЭКО-СИСТЕМУ!</h1>
        <p class="lead"></p>
    </div>

</div>
