<?php

$config['controllerNamespace'] = 'app\commands';


$config['components']['errorHandler'] = [
	'class' => 'ladamalina\yii2_rollbar\ConsoleErrorHandler',
];

$config['bootstrap'][] = 'log';
$config['bootstrap'][] = 'app\components\bootstrap\EventBootstrap';
