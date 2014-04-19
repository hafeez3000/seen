<?php

$config['components']['user'] = [
	'identityClass' => 'app\models\User',
	'enableAutoLogin' => true,
];

$config['components']['errorHandler'] = [
	'errorAction' => 'site/error',
];

$config['components']['assetManager'] = [
	'bundles' => [
		'yii\bootstrap\BootstrapAsset' => [
			'css' => [],
		],
	],
];