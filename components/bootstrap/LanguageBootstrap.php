<?php namespace app\components\bootstrap;

use \Yii;
use \yii\base\Application;
use \yii\base\BootstrapInterface;

class LanguageBootstrap implements BootstrapInterface
{
	protected function getDefaultLanguage()
	{
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
			return $this->parseDefaultLanguage($_SERVER['HTTP_ACCEPT_LANGUAGE']);
		else
			return $this->parseDefaultLanguage(NULL);
	}

	protected function parseDefaultLanguage($http_accept, $deflang = 'en')
	{
		if (isset($http_accept) && strlen($http_accept) > 1) {
			$x = explode(',',$http_accept);
			foreach ($x as $val) {

			if(preg_match('/(.*);q=([0-1]{0,1}.d{0,4})/i', $val, $matches))
				$lang[$matches[1]] = (float)$matches[2];
			else
				$lang[$val] = 1.0;
			}

			$qval = 0.0;
			foreach ($lang as $key => $value) {
				if ($value > $qval) {
					$qval = (float)$value;
					$deflang = $key;
				}
			}
		}

		if (strpos($deflang, '-') !== false)
			$deflang = substr($deflang, 0, strpos($deflang, '-'));

		return strtolower($deflang);
	}

	/**
	 * @inheritdoc
	 */
	public function bootstrap($app)
	{
		// Set language
		$language = 'en';

		if (Yii::$app->user->isGuest) {
			if (Yii::$app->session->get('language', false) === false) {
				$language = $this->getDefaultLanguage();
				Yii::$app->session->set('language', $language);
			} else {
				$language = Yii::$app->session->get('language');
			}
		} else {
			if (isset(Yii::$app->user->identity->language->iso))
				$language = Yii::$app->user->identity->language->iso;
			else
				$language = Yii::$app->params['lang']['default'];
		}

		$app->language = $language;
	}
}