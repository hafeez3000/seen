<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
        'id' => 'seen-console',
        'components' => [
                'db' => $db,
        ],
        'params' => $params,
];

require(__DIR__ . '/../common/common.php');
require(__DIR__ . '/../common/console.php');

$config['components']['cache'] = [
        'class' => 'yii\caching\FileCache',
];

$config['components']['urlManager']['baseUrl'] = 'http://seen.app/';
$config['components']['log']['flushInterval'] = 100;

return $config;
