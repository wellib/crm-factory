<?php
use yii\helpers\Html;
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <?= Html::csrfMetaTags() ?>
        <title></title>
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>
    <?= $content ?>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>
