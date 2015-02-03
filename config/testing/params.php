<?php

return [
	'themoviedb' => [
		'key' => '',
		'url' => 'https://api.themoviedb.org/',
		'image_url' => 'https://image.tmdb.org/t/p/',
	],
	'email' => [
		'admin' => 'admin@seenapp.com',
		'system' => 'no-reply@seenapp.com',
		'mandrill' => [
			'baseUrl' => 'https://mandrillapp.com/api/1.0/',
			'apikey' => '',
			'globalMergeVars' => [
			],
		],
	],
	'mailchimp' => [
		'apikey' => '',
		'listid' => '',
	],
	'salt' => '123456',
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
			'key' => '',
			'secret' => '',
		],
	],
	'redis' => [
		'host' => '127.0.0.1',
		'port' => 6379,
	],
	'prediction' => [
		'key' => '',
	],
	'adsense' => [
		'client' => '',
		'slot' => '',
	],
];
