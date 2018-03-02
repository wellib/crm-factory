<?php

namespace app\assets;

use yii\web\AssetBundle;

class SortableJsAsset extends AssetBundle
{
    public $sourcePath = '@npm/sortablejs';
    public function init()
    {
        $this->js[] = YII_ENV_DEV ? 'Sortable.js' : 'Sortable.min.js';
    }

    public $depends = [
        
    ];
}
