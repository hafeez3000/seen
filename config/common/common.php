<?php

$config['name'] = 'SEEN';
$config['basePath'] = dirname(dirname(__DIR__));
$config['language'] = 'en';
$config['bootstrap'] = [];

$config['extensions'] = require(__DIR__ . '/../../vendor/yiisoft/extensions.php');

$config['components']['urlManager'] = [
	'enablePrettyUrl' => true,
	'showScriptName' => false,
	'rules' => [
		// TV
		'tv' => 'tv/index',
		'tv/load' => 'tv/load',
		'tv/subscribe/<slug:.*?>' => 'tv/subscribe',
		'tv/unsubscribe/<slug:.*?>' => 'tv/unsubscribe',
		'tv/archive' => 'tv/archive',
		'tv/archive/<slug:.*?>' => 'tv/archive-show',
		'tv/unarchive/<slug:.*?>' => 'tv/unarchive-show',
		'tv/<slug:.*?>' => 'tv/view',

		// Movies
		'movies' => 'movie/index',
		'movie/load' => 'movie/load',
		'movie/watch/<slug:.*?>' => 'movie/watch',
		'movie/unwatch/<id:\d+>' => 'movie/unwatch',
		'movie/<slug:.*?>' => 'movie/view',

		// Oauth
		'login/oauth/authorize' => 'oauth/authorize',
		'POST login/oauth/access_token' => 'oauth/access-token',

		// Site
		'login' => 'site/login',
		'logout' => 'site/logout',
		'account' => 'user/account',

		'language/<iso:.*?>' => 'site/language',

		'reset-password' => 'site/reset',
		'reset-password/<token:.*?>' => 'site/reset-password',

		// Developer resources
		'dev' => 'developer/index',

		'dev/consumer' => 'oauth-application/index',
		'dev/consumer/<id:\d+>' => 'oauth-application/view',
		'dev/consumer/<id:\d+>/<action:\w+>' => 'oauth-application/<action>',
		'dev/consumer/<id:\d+>/regenerate' => 'oauth-application/regenerate',
		'dev/consumer/create' => 'oauth-application/create',

		// API version 1
		'GET api/v1/user' => 'api-v1/user',
		'PATCH api/v1/user' => 'api-v1/update-user',
		'GET api/v1/user/permissions' => 'api-v1/permissions',
		'GET api/v1/movies' => 'api-v1/movies',
		'POST api/v1/movies/<id:\d+>/<iso:\w+>' => 'api-v1/movie-watch',
		'GET api/v1/shows' => 'api-v1/shows',
		'GET api/v1/shows/<id:\d+>/<iso:\w+>' => 'api-v1/watched-episodes',
		'POST api/v1/shows/<id:\d+>/<iso:\w+>' => 'api-v1/show-subscribe',
		'DELETE api/v1/shows/<id:\d+>/<iso:\w+>' => 'api-v1/show-unsubscribe',
		'POST api/v1/shows/<id:\d+>/<iso:\w+>/<season:\d+>/<episode:\d+>' => 'api-v1/episode-watch',
		'DELETE api/v1/shows/<id:\d+>/<iso:\w+>/<season:\d+>/<episode:\d+>' => 'api-v1/episode-unwatch',

		// Base routes
		'<controller:\w+>/<id:\d+>' => '<controller>/view',
		'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
		'<controller:\w+>/<action:\d+>' => '<controller>/<action>',
	],
];

$config['components']['i18n'] = [
	'translations' => [
		'*' => [
			'class' => 'yii\i18n\PhpMessageSource',
			'forceTranslation' => true,
		],
	],
];

$config['components']['authManager'] = [
	'class' => 'yii\rbac\DbManager',
];

if (!YII_ENV_TEST) {
	$config['components']['log'] = [
		'traceLevel' => YII_DEBUG ? 3 : 0,
		'targets' => [
			'file' => [ // Log errors to file as a fallback
				'class' => 'yii\log\FileTarget',
				'levels' => ['error'],
			],
			'db' => [ // Log important messages to database
				'class' => 'yii\log\DbTarget',
				'levels' => ['error', 'warning'],
			],
			'mail' => [ // Log emails to database
				'class' => 'yii\log\DbTarget',
				'levels' => ['error', 'warning', 'info'],
				'categories' => ['application\mail'],
			],
			'sync' => [ // Log sync messages to database
				'class' => 'yii\log\DbTarget',
				'levels' => ['error', 'warning', 'info'],
				'categories' => ['application\sync'],
			],
			'bugsnag' => [ // Log errors to bugsnag
				'class' => 'app\components\BugsnagLogger',
				'levels' => ['error', 'warning'],
			]
		],
	];
}

if (YII_ENV_DEV) {
	$config['bootstrap'][] = 'debug';
	$config['modules']['debug'] = 'yii\debug\Module';
	$config['modules']['gii'] = 'yii\gii\Module';
}