<?php namespace app\assets;

use \Yii;
use \yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
	public $basePath = '@webroot';
	public $baseUrl = '@web';

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
		if (Yii::$app->language != 'en')
			$this->js[] = 'components/select2/select2_locale_' . Yii::$app->language . '.js';

		return parent::init();
	}
}
