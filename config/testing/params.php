<?php

return [
	'themoviedb' => [
		'key' => 'dd0e56e71b647ad22a60aa57ce20e8b9',
		'url' => 'http://private-d0eb7-themoviedb.apiary-proxy.com/3',
	],
	'email' => [
		'admin' => 'admin@seenapp.com',
		'mandrill' => [
			'baseUrl' => 'https://mandrillapp.com/api/1.0/',
			'apikey' => '6a7b5efb-e419-488f-b649-3afc33289439',
			'globalMergeVars' => [
				[
					'name' => 'current_year',
					'content' => date('Y'),
				],
				[
					'name' => 'company',
					'content' => 'VisualAppeal',
				],
			],
		],
	],
	'mailchimp' => [
		'apikey' => '138d4b5a4f45ea34595756024dc6c1d8-us6',
		'listid' => '3605933162',
	],
];
