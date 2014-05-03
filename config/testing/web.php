<?php

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config['components']['cache'] = [
	'class' => 'yii\caching\DummyCache',
];

$config = [
	'id' => 'seen',
	'params' => $params,
	'components' => [
		'db' => $db,
	],
];

require(__DIR__ . '/../common/common.php');
require(__DIR__ . '/../common/web.php');

return $config;
