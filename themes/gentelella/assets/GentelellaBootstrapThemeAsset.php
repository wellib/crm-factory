<?php

namespace app\themes\gentelella\assets;

use yii\web\AssetBundle;

class GentelellaBootstrapThemeAsset extends AssetBundle
{
    public $sourcePath = '@theme/vendor/bower/gentelella';
    public $css = [
        'build/css/custom.min.css',
        'vendors/iCheck/skins/flat/green.css',
        'vendors/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.min.css',
        'css/site.css',
    ];
    public $js = [
        'vendors/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js',
        'vendors/iCheck/icheck.min.js',
        'build/js/custom.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'app\themes\gentelella\assets\FontAwesomeAsset',
    ];

    public function getUserAvatarDefault()
    {
        return $this->baseUrl . '/production/images/user.png';
    }
}
