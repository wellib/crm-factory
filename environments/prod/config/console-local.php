<?php

$db = require(__DIR__ . '/db-local.php');
$mongodb = require(__DIR__ . '/mongodb-local.php');

$config = [
    'components' => [
        'db' => $db,
        'mongodb' => $mongodb,
    ],
];

return $config;