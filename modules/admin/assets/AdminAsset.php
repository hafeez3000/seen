<?php namespace app\modules\admin\assets;

use \Yii;
use \yii\web\AssetBundle;

class AdminAsset extends AssetBundle
{
	public $sourcePath = '@module';

	public $js = [
		'js/app.min.js',
	];

	public $depends = [
		'app\assets\AppAsset',
	];
}
