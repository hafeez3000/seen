<?php

$params = require(__DIR__ . '/params.php');

$config = [
	'id' => 'seen',
	'params' => $params,
];

require(__DIR__ . '/../common/common.php');
require(__DIR__ . '/../common/web.php');

return $config;
