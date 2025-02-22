<?php

$config['name'] = 'SEEN';
$config['basePath'] = dirname(dirname(__DIR__));
$config['bootstrap'] = [
	'rollbar',
];

$config['extensions'] = require(__DIR__ . '/../../vendor/yiisoft/extensions.php');

$config['components']['urlManager'] = [
	'baseUrl' => 'https://seenapp.com/',
	'enablePrettyUrl' => true,
	'showScriptName' => false,
	'rules' => [
		'imprint' => 'site/imprint',
		'privacy' => 'site/privacy',
		'contact' => 'site/contact',

		// TV
		'tv' => 'tv/index',
		'tv/load' => 'tv/load',
		'tv/popular' => 'tv/popular',
		'tv/dashboard' => 'tv/dashboard',
		'tv/recommend' => 'tv/recommend',
		'tv/subscribe/<slug:.*?>' => 'tv/subscribe',
		'tv/unsubscribe/<slug:.*?>' => 'tv/unsubscribe',
		'tv/archive' => 'tv/archive',
		'tv/archive/<slug:.*?>' => 'tv/archive-show',
		'tv/unarchive/<slug:.*?>' => 'tv/unarchive-show',
		'tv/sync' => 'tv/sync',
		'tv/syncSeason' => 'tv/sync-season',
		'tv/<slug:.*?>/rate/<rating:\d+>' => 'tv/rate',
		'tv/<slug:.*?>' => 'tv/view',

		'GET episode/seen/<id:\d+>' => 'episode/seen',
		'GET episode/unseen/<id:\d+>' => 'episode/unseen',

		'POST episode/seen' => 'episode/seen',
		'POST episode/unseen' => 'episode/unseen',

		// Movies
		'movies' => 'movie/index',
		'movie/load' => 'movie/load',
		'movie/popular' => 'movie/popular',
		'movie/dashboard' => 'movie/dashboard',
		'movie/watch/<slug:.*?>' => 'movie/watch',
		'movie/unwatch/<id:\d+>' => 'movie/unwatch',
		'movie/<slug:.*?>/rate/<rating:\d+>' => 'movie/rate',
		'movie/<slug:.*?>' => 'movie/view',

		'watchlist/add/<slug:.*?>' => 'watchlist/add',
		'watchlist/remove/<slug:.*?>' => 'watchlist/remove',

		// Persons
		'person/load' => 'person/load',
		'person/<slug:.*?>' => 'person/view',

		// Oauth
		'login/oauth/authorize' => 'oauth/authorize',
		'POST login/oauth/access_token' => 'oauth/access-token',

		// Site
		'login' => 'site/login',
		'login/<service:.*?>' => 'site/oauth',

		'sign-up' => 'site/sign-up',

		'logout' => 'site/logout',

		// Account
		'account' => 'user/account',
		'auth/themoviedb' => 'auth/themoviedb',
		'auth/themoviedb/callback' => 'auth/themoviedb-callback',
		'auth/themoviedb/sync' => 'auth/themoviedb-sync',

		// Admin
		'admin' => 'admin/site/index',

		// Languages
		'language/<iso:.*?>' => 'site/language',

		// Reset password
		'reset-password' => 'site/reset',
		'reset-password/<token:.*?>' => 'site/reset-password',

		// Developer resources
		'dev' => 'developer/index',

		'dev/consumer' => 'oauth-application/index',
		'dev/consumer/<id:\d+>' => 'oauth-application/view',
		'dev/consumer/<id:\d+>/<action:\w+>' => 'oauth-application/<action>',
		'dev/consumer/<id:\d+>/regenerate' => 'oauth-application/regenerate',
		'dev/consumer/create' => 'oauth-application/create',

		// Public profile
		'profile/<profile:\d+\-\w+>' => 'profile/index',
		'profile/<profile:\d+\-\w+>/movies' => 'profile/movie',
		'profile/<profile:\d+\-\w+>/tv-shows' => 'profile/tv',

		// Lists
		'lists' => 'lists/index',
		'list/create' => 'lists/create',
		'list/<slug:.*?>/update' => 'lists/update',
		'list/<slug:.*?>/delete' => 'lists/delete',
		'list/<slug:.*?>/entry/add' => 'lists-entry/create',
		'list/<slug:.*?>' => 'lists/view',

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
		'GET api/v1/unseen-episodes' => 'api-v1/unseen-episodes',

		// Base module routes
		'<module:\w+>/<controller:\w+>/<id:\d+>' => '<module>/<controller>/view',
		'<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<controller>/<action>',
		'<module:\w+>/<controller:\w+>/<action:\d+>' => '<module>/<controller>/<action>',

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
		],
	],
];

$config['components']['authManager'] = [
	'class' => 'yii\rbac\DbManager',
];

$config['components']['cache'] = [
	'class' => 'yii\caching\DummyCache',
];

$config['components']['rollbar'] = [
	'class' => 'ladamalina\yii2_rollbar\RollbarComponent',
	'accessToken' => isset($rollbarAccessToken) ? $rollbarAccessToken : '',
	'environment' => YII_ENV,
];

$config['components']['mailer'] = [
	'class' => 'yii\swiftmailer\Mailer',
];

$config['modules']['admin'] = [
	'class' => 'app\modules\admin\Module',
];

if (!YII_ENV_TEST) {
	// Dev or prod env
	$config['components']['log'] = [
		'traceLevel' => YII_DEBUG ? 3 : 0,
		'targets' => [
			'file' => [ // Log errors to file as a fallback
				'class' => 'yii\log\FileTarget',
				'levels' => ['error'],
			],
			'db' => [ // Log important messages to database
				'class' => 'yii\log\DbTarget',
				'logTable' => '{{%log}}',
				'db' => 'db',
				'levels' => ['error', 'warning'],
			],
			'app' => [ // Log mail/sync info messages to database
				'class' => 'yii\log\DbTarget',
				'levels' => ['info'],
				'categories' => [
					'application\sync'
				],
			],
		],
	];
} else {
	// Testing env
	$config['components']['mailer'] = [
		'class' => 'yii\swiftmailer\Mailer',
		'useFileTransport' => true,
	];
}
