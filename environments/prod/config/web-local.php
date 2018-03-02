<?php

$db = require(__DIR__ . '/db-local.php');
$mongodb = require(__DIR__ . '/mongodb-local.php');
$params = require(__DIR__ . '/params-local.php');

$config = [
    'components' => [
        'db' => $db,
        'mongodb' => $mongodb,
        //'reCaptcha' => [
        //    'name' => 'reCaptcha',
        //    'class' => 'himiklab\yii2\recaptcha\ReCaptcha',
        //    'siteKey' => '6LeVgiITAAAAADUtEkrz571Vquu50iaT79DTSf8H',
        //    'secret' => '6LeVgiITAAAAAHpIeImFvJ9wN4dvlPyaISYxD8e4',
        //],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => $params['smtpEmail'],
                'password' => 'uW5rSF%M',
                'port' => '587',
                'encryption' => 'tls',
            ],
        ],
    ],
    'params' => $params,
];

return $config;