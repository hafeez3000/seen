<?php namespace app\components;

use \Yii;

class Mailchimp extends \Mailchimp
{
	public function __construct() {
		parent::__construct(Yii::$app->params['mailchimp']['apikey'], [
			'debug' => defined('YII_DEBUG') ? true : false,
		]);
	}

	public function subscribe($email) {
		$list = new \Mailchimp_Lists($this);

		try {
			$list->subscribe(
				Yii::$app->params['mailchimp']['listid'],
				['email' => $email],
				[
					'optin_ip' => Yii::$app->request->userIP,
					'mc_language' => Yii::$app->language,
				],
				'html',
				false,
				false,
				false,
				false
			);
		} catch (\Mailchimp_Error $e) {
			Yii::error("Error while subscribing email {$email} to mailchimp: {$e->getMessage()}", 'application\email');
			return false;
		}

		return true;
	}
}