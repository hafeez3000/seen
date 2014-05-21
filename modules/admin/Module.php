<?php namespace app\modules\admin;

use \Yii;
use \yii\base\Module as BaseModule;

class Module extends BaseModule
{
	public function init()
	{
		Yii::$app->errorHandler->errorAction = '/admin/site/error';

		return parent::init();
	}
}
