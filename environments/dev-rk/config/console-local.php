<?php

$db = require(__DIR__ . '/db-local.php');
$mongodb = require(__DIR__ . '/mongodb-local.php');

$config = [
    'components' => [
        'db' => $db,
        'mongodb' => $mongodb,
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;