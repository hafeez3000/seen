<?php

$config['name'] = 'SEEN';
$config['basePath'] = dirname(dirname(__DIR__));
$config['bootstrap'] = ['log'];
$config['language'] = 'de';

$config['extensions'] = require(__DIR__ . '/../../vendor/yiisoft/extensions.php');

$config['components']['cache'] = [
	'class' => 'yii\caching\DummyCache',
];

$config['components']['urlManager'] = [
	'enablePrettyUrl' => true,
	'showScriptName' => false,
	'rules' => [
		'tv' => 'tv/index',
		'tv/load' => 'tv/load',
		'tv/subscribe/<slug:.*?>' => 'tv/subscribe',
		'tv/unsubscribe/<slug:.*?>' => 'tv/unsubscribe',
		'tv/archive' => 'tv/archive',
		'tv/archive/<slug:.*?>' => 'tv/archive-show',
		'tv/unarchive/<slug:.*?>' => 'tv/unarchive-show',
		'tv/<slug:.*?>' => 'tv/view',

		'login' => 'site/login',
		'logout' => 'site/logout',
		'account' => 'user/account',

		'movies' => 'movies/index',
		'movies/<slug:.*?>' => 'movies/view',

		'<controller:\w+>/<id:\d+>' => '<controller>/view',
		'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
		'<controller:\w+>/<action:\d+>' => '<controller>/<action>',
	],
];

$config['components']['log'] = [
	'traceLevel' => YII_DEBUG ? 3 : 0,
	'targets' => [
		'file' => [
			'class' => 'yii\log\FileTarget',
			'levels' => ['error', 'warning'],
		],
		'db' => [
			'class' => 'yii\log\DbTarget',
			'levels' => ['error', 'warning'],
		],
		'mail' => [
			'class' => 'yii\log\DbTarget',
			'levels' => ['error', 'warning', 'info'],
			'categories' => ['application\mail'],
		],
		'sync' => [
			'class' => 'yii\log\DbTarget',
			'levels' => ['error', 'warning', 'info'],
			'categories' => ['application\sync'],
		]
	],
];

$config['components']['i18n'] = [
	'translations' => [
		'*' => [
			'class' => 'yii\i18n\PhpMessageSource',
		],
	],
];

if (YII_ENV_DEV) {
	$config['bootstrap'][] = 'debug';
	$config['modules']['debug'] = 'yii\debug\Module';
	$config['modules']['gii'] = 'yii\gii\Module';
}