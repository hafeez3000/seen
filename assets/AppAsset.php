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
		'js/vendor.min.js',
		'js/app.min.js',
	];

	public $jsOptions = [
		'position' => \yii\web\View::POS_HEAD
	];
}
