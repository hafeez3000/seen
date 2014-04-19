<?php namespace app\assets;

use yii\web\AssetBundle;

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
}
