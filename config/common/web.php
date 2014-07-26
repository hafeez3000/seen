<?php

$baseUrl = dirname($_SERVER['SCRIPT_NAME']);
if ($baseUrl == '/')
	$baseUrl = '';

$config['bootstrap'][] = 'app\components\bootstrap\MaintenanceBootstrap';
$config['bootstrap'][] = 'log';
$config['bootstrap'][] = 'app\components\bootstrap\EventBootstrap';
$config['bootstrap'][] = 'app\components\bootstrap\LanguageBootstrap';

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
		'yii\bootstrap\BootstrapPluginAsset' => [
			'js' => [],
		],
		'yii\web\JqueryAsset' => [
			//'sourcePath' => null,
			'js' => [$baseUrl . '/js/jquery.min.js'],
		],
	],
];

$config['components']['request'] = [
	'cookieValidationKey' => 'ulht1utnbaliHGHKABHha89124bujlksaf',
];

$config['modules']['gridview'] = [
	'class' => '\kartik\grid\Module'
];
