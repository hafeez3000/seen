<?php

return [
	'themoviedb' => [
		'key' => '',
		'url' => '',
		'image_url' => 'https://image.tmdb.org/t/p/',
	],
	'email' => [
		'admin' => 'admin@seenapp.com',
		'system' => 'noreply@seenapp.com',
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
		'en' => [
			'datetime' => 'Y\\\m\\\d H:i',
			'date' => 'Y\\\m\\\d',
			'decPoint' => '.',
			'thousandsSep' => ',',
		],
	],
	'oauth' => [
		'facebook' => [
			'key' => '',
			'secret' => '',
		]
	],
	'redis' => [
		'host' => '127.0.0.1',
		'port' => 6379,
	],
];
