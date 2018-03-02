<?php

use yii\helpers\Html;
use app\themes\gentelella\assets\GentelellaBootstrapThemeAsset as ThemeAsset;

/* @var $this \yii\web\View */
/* @var $content string */

ThemeAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?php echo 'ЭКО-СИСТЕМА'; // Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="nav-md">
<?php $this->beginBody() ?>
<?= $content ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
