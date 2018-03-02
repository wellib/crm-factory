<?php

namespace app\themes\gentelella\assets;

use yii\web\AssetBundle;


class FontAwesomeAsset extends AssetBundle
{
    public $sourcePath = '@theme/vendor/bower/font-awesome';
    public $css = [
        'css/font-awesome.min.css',
    ];

    public $depends = [

    ];
}
