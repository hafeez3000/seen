<?php namespace app\components\bootstrap;

use \Yii;
use \yii\base\Application;
use \yii\base\BootstrapInterface;

use \app\models\Language;

class LanguageBootstrap implements BootstrapInterface
{
	public function getDefaultLanguage()
	{
		$language = new \Browser\Language;
		return $language->getLanguage();
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
				$language = $this->getDefaultLanguage();
			}
		} else {
			if (isset(Yii::$app->user->identity->language->iso))
				$language = Yii::$app->user->identity->language->iso;
			else
				$language = $this->getDefaultLanguage();
		}

		$exists = Language::find()
			->where(['iso' => $language])
			->exists();

		if ($exists)
			$app->language = $language;
		else
			$app->language = $app->params['lang']['default'];
	}
}