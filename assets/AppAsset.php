<?php namespace app\assets;

use \Yii;
use \yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
	public $basePath = '@webroot';
	public $baseUrl = '@web';

	public $select2Languages = [
		'ar',
		'bg',
		'ca',
		'cs',
		'da',
		'de',
		'el',
		'es',
		'et',
		'eu',
		'fa',
		'fi',
		'fr',
		'gl',
		'he',
		'hr',
		'hu',
		'id',
		'is',
		'it',
		'ja',
		'ka',
		'ko',
		'lt',
		'lv',
		'mk',
		'ms',
		'nl',
		'no',
		'pl',
		'pt',
		'ro',
		'rs',
		'ru',
		'sk',
		'sv',
		'th',
		'tr',
		'uk',
		'vi',
		'zh',
	];

	public $css = [
		'css/app.min.css',
	];

	public $js = [
		'js/app.min.js',
	];

	public $depends = [
		'yii\web\YiiAsset',
		'yii\web\JqueryAsset',
	];

	public function init() {
		if (Yii::$app->language != 'en' && in_array(Yii::$app->language, $this->select2Languages))
			$this->js[] = 'components/select2/select2_locale_' . Yii::$app->language . '.js';

		return parent::init();
	}
}
