<?php

return [
	'baseUrl' => 'http://seen.app',
	'remote' => [
		'host' => '12.34.56.78',
		'username' => 'seen',
		'key' => '/home/vagrant/.ssh/id_rsa',
	],
	'themoviedb' => [
		'key' => '123456789abcdefghijklmnopqrstuvwxyz',
		'url' => 'https://api.themoviedb.org/3',
		'image_url' => 'https://image.tmdb.org/t/p/',
	],
	'email' => [
		'admin' => 'admin@seenapp.com',
		'system' => 'no-reply@seenapp.com',
		'mandrill' => [
			'baseUrl' => 'https://mandrillapp.com/api/1.0/',
			'apikey' => 'invalid',
			'globalMergeVars' => [
			],
		],
	],
	'mailchimp' => [
		'apikey' => '',
		'listid' => '',
	],
	'salt' => '$6$rounds=10000$12345678abcdefg$',
	'lang' => [
		'default' => 'en-US',
		'default_iso' => 'en',
		'en' => [
			'datetime' => 'd/m/Y H:i',
			'date' => 'd/m/Y',
			'decPoint' => '.',
			'thousandsSep' => ',',
		],
		'en-US' => [
						'datetime' => 'd/m/Y H:i',
						'date' => 'd/m/Y',
						'decPoint' => '.',
						'thousandsSep' => ',',
				],
		'de' => [
			'datetime' => 'd.m.Y H:i',
			'date' => 'd.m.Y',
			'decPoint' => ',',
			'thousandsSep' => '.',
		],
	],
	'oauth' => [
		'facebook' => [
			'key' => '1234567890abc',
			'secret' => '1234567890abc1234567890abc1234567890abc',
		],
	],
	'redis' => [
		'host' => '127.0.0.1',
		'port' => 6379,
	],
	'prediction' => [
		'key' => '',
		'eventserver' => '',
	],
];
