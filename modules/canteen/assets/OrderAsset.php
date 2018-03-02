<?php

namespace app\modules\canteen\assets;

use yii\web\AssetBundle;

class OrderAsset extends AssetBundle
{
    public $sourcePath = __DIR__;
    public $css = [
    ];
    public $js = [
        'js/order.js',
    ];
    public $depends = [
        'app\themes\gentelella\assets\GentelellaBootstrapThemeAsset',
    ];
}
