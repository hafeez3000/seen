<?php

$env = isset($_SERVER['APPLICATION_ENV']) ? $_SERVER['APPLICATION_ENV'] : 'development';

if ($env == 'development') {
	defined('YII_DEBUG') or define('YII_DEBUG', true);
	defined('YII_ENV') or define('YII_ENV', 'dev');
}

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

require(__DIR__ . '/../components/Application.php');

$config = require(__DIR__ . '/../config/' . $env . '/web.php');

(new \app\components\Application($config))->run();
