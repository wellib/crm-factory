<?php

namespace app\assets;

use yii\web\AssetBundle;

class VueSortableAsset extends AssetBundle
{
    public $sourcePath = '@npm/vue-sortable';
    public function init()
    {
        $this->js[] = YII_ENV_DEV ? 'vue-sortable.js' : 'vue-sortable.js';
    }

    public $depends = [
        'app\assets\SortableJsAsset',
        'app\assets\VueAsset',
    ];
}
