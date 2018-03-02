<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests/codeception');

$params = require(__DIR__ . '/params.php');
$modules = require(__DIR__ . '/modules.php');

$config = [
    'id' => 'crm-kz-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ru-RU',
    //'language' => 'en-US',
    'sourceLanguage' => 'sys', // системный язык для перевода
    'timeZone' => 'Etc/GMT-6', // Europe/Kiev,Europe/Moscow
    'controllerNamespace' => 'app\commands',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'authManager' => [
            'class' => 'yii\mongodb\rbac\MongoDbManager',
            'defaultRoles' => ['guest'],
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'sys',
                    'fileMap' => [
                        'app'       => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
    ],

    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

foreach ($modules as $name => $module) {
    if (!isset($module['class'])) {
        continue;
    }
    if (isset($module['console'])) {
        $config['modules'][$name] = array_merge([
            'class' => $module['class']
        ], $module['console']['params']);
        if (isset($module['console']['bootstrap']) && $module['console']['bootstrap'] === true) {
            $config['bootstrap'][] = $name;
        }
    }
}

return $config;
