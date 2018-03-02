<?php

namespace app\themes\gentelella\assets;


class BootstrapDialogAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@theme/vendor/bower/bootstrap3-dialog/dist';
    public $css = [
        'css/bootstrap-dialog.min.css',
    ];

    public $js = [
        'js/bootstrap-dialog.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
