<?php

use yii\web\View;

use app\assets\VueAsset;
use app\assets\VueSortableAsset;
use app\assets\SortableJsAsset;

/* @var $this View */

use execut\widget\TreeView;
use yii\web\JsExpression;

$data = [
    [
        'text' => 'Parent 1',
        'nodes' => [
            [
                'text' => 'Child 1',
                'icon' => 'glyphicon glyphicon-stop',
                'nodes' => [
                    [
                        'text' => 'Grandchild 1'
                    ],
                    [
                        'text' => 'Grandchild 2'
                    ]
                ]
            ],
            [
                'text' => 'Child 2',
            ]
        ],
    ],
    [
        'text' => 'Parent 2',
    ]
];

$onSelect = new JsExpression(<<<JS
function (undefined, item) {
    console.log(item);
}
JS
);
$groupsContent = TreeView::widget([
    'data' => $data,
    'size' => TreeView::SIZE_NORMAL,
    'header' => 'Categories',
    'searchOptions' => [
        'inputOptions' => [
            'placeholder' => 'Search category...'
        ],
    ],
    'clientOptions' => [
        'onNodeSelected' => $onSelect,
        'selectedBackColor' => 'rgb(40, 153, 57)',
        'borderColor' => '#fff',
    ],
]);


echo $groupsContent;