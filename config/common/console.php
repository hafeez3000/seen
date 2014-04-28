<?php

$config['controllerNamespace'] = 'app\commands';

$config['bootstrap'][] = 'log';
$config['bootstrap'][] = 'app\components\bootstrap\BugsnagBootstrap';
$config['bootstrap'][] = 'app\components\bootstrap\EventBootstrap';