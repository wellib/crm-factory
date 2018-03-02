<?php

namespace app\modules\accounts\assets;

use yii\web\AssetBundle;

class UserStructureAsset extends AssetBundle
{
    public $sourcePath = __DIR__;

    public $js = [
        'js/user-structure.js',
    ];

    public $depends = [
        'app\assets\FancytreeAsset',
    ];
}
