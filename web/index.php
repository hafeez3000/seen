<?php
date_default_timezone_set('UTC');

$env = isset($_SERVER['APPLICATION_ENV']) ? $_SERVER['APPLICATION_ENV'] : 'development';

if ($env == 'development') {
	defined('YII_DEBUG') || define('YII_DEBUG', true);
	defined('YII_ENV') || define('YII_ENV', 'dev');
}

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/' . $env . '/web.php');

(new \yii\web\Application($config))->run();
