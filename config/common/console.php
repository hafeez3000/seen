<?php

$config['controllerNamespace'] = 'app\commands';

$config['bootstrap'] = [
	'log',
	'app\components\bootstrap\BugsnagBootstrap',
	'app\components\bootstrap\EventBootstrap',
];