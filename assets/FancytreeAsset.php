<?php

namespace app\assets;

use yii\web\AssetBundle;

class FancytreeAsset extends AssetBundle
{
    public $sourcePath = '@vendor/bower/fancytree';
    public $css = [
        'dist/skin-bootstrap/ui.fancytree.min.css',
    ];
    public $js = [
        'dist/jquery.fancytree.js',
        'dist/src/jquery.fancytree.dnd.js',
        'dist/src/jquery.fancytree.edit.js',
        'dist/src/jquery.fancytree.glyph.js',
        'dist/src/jquery.fancytree.table.js',
        'dist/src/jquery.fancytree.wide.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
    ];
}
