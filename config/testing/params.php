<?php

return [
	'themoviedb' => [
		'key' => '',
		'url' => '',
		'image_url' => '',
	],
	'email' => [
		'admin' => 'admin@seenapp.com',
		'system' => 'no-reply@seenapp.com',
		'mandrill' => [
			'baseUrl' => 'https://mandrillapp.com/api/1.0/',
			'apikey' => '6a7b5efb-e419-488f-b649-3afc33289439',
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
];
