<?php

$params = require(__DIR__ . '/params.php');
$modules = require(__DIR__ . '/modules.php');

$config = [
    'id' => 'crm-kz',
    'name' => 'ЭКО-СИСТЕМА',
    'basePath' => dirname(__DIR__),

    'language' => 'ru-RU',
    'sourceLanguage' => 'sys', // системный язык для перевода
    'timeZone' => 'Etc/GMT-6', // Europe/Kiev,Europe/Moscow
    //'timeZone' => 'Europe/Kiev', // Europe/Kiev,Europe/Moscow
    'modules' => [
        'gridview' => 'kartik\grid\Module',
        'redactor' => [
            'class' => 'yii\redactor\RedactorModule',
            'uploadDir' => '@webroot/uploads/images',
            'uploadUrl' => '@web/uploads/images',
            'imageAllowExtensions' => ['jpg','png','gif']
        ],
    ],
    'bootstrap' => ['log'],

    'aliases' => [
        '@theme' => '@app/themes/gentelella',
    ],

    'defaultRoute' => 'admin/default/index',

    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'A3y9VVI9EHtKJHI2-L6qSv1ik1byht3c',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        //'user' => [
        //    'identityClass' => 'app\models\User',
        //    'enableAutoLogin' => true,
        //],

        'user' => [
            'identityClass' => '\app\modules\accounts\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['/accounts/user/signin'],
        ],

        'authManager' => [
            'class' => 'yii\mongodb\rbac\MongoDbManager',
            'defaultRoles' => ['guest'],
        ],

        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true, // строгое соответствие описанным rules
            // в данном случае автоматические роуты типа:
            //   - <controller>/<action>
            //   - <module>/<controller>/<action>
            // больше не работают и все роуты нужно писать вручную
            //'suffix' => '/', // делает так что бы в конце ура всегда был /(slash) - хз зачем это нужно, мб seo, но клиент требует
            'rules' => [
                '' => 'site/index',
                'redactor/upload/<action>' => 'redactor/upload/<action>', // если enableStrictParsing = true, то теряются роуты для yii\redactor\RedactorModule, поэтому нужно вручную писать
                //'redactor/upload/file' => 'redactor/upload/file', // если enableStrictParsing = true, то теряются роуты для yii\redactor\RedactorModule, поэтому нужно вручную писать
                //'redactor/upload/file' => 'redactor/upload/image-json', // если enableStrictParsing = true, то теряются роуты для yii\redactor\RedactorModule, поэтому нужно вручную писать
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

        'view' => [
            'theme' => [
                'basePath' => '@theme',
                'baseUrl' => '@theme',
                'pathMap' => [
                    '@app/views' => '@theme/views',
                    '@app/modules' => '@theme/modules',
                ],
            ],
        ],

        'formatter' => [
            'dateFormat' => 'DD.MM.YYYY',
        ],

        'assetManager' => [
            'forceCopy' => !YII_DEBUG,
        ],
    ],
    'params' => $params,
];

foreach ($modules as $name => $module) {
    if (!isset($module['class'])) {
        continue;
    }
    if (isset($module['web'])) {
        $config['modules'][$name] = array_merge([
            'class' => $module['class']
        ], $module['web']['params']);
        if (isset($module['web']['bootstrap']) && $module['web']['bootstrap'] === true) {
            $config['bootstrap'][] = $name;
        }
    }

}


return $config;
