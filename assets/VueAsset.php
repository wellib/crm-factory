<?php

namespace app\assets;

use yii\web\AssetBundle;

class VueAsset extends AssetBundle
{
    public $sourcePath = '@bower/vue/dist';
    public function init()
    {
        $this->js[] = YII_ENV_DEV ? 'vue.js' : 'vue.min.js';
    }
}
