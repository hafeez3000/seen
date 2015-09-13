<?php namespace app\components;

use \Yii;
use \Mixpanel;

class YiiMixpanel
{
	protected static $mp = null;

	protected static function init()
	{
		if (isset(Yii::$app->params['mixpanel']) && self::$mp === null)
			self::$mp =  Mixpanel::getInstance(Yii::$app->params['mixpanel']);
		else
			return null;
	}

	public static function track($event, array $data = array())
	{
		self::init();
		if (self::$mp === null)
			return;

		self::$mp->track($event, $data);
	}

	public static function register($property, array $data)
	{
		self::init();
		if (self::$mp === null)
			return;

		self::$mp->registerAll($property, $data);
	}

	public static function identify($id)
	{
		self::init();
		if (self::$mp === null)
			return;

		self::$mp->identify($id);
	}

	public static function createAlias($oldId, $newId)
	{
		self::init();
		if (self::$mp === null)
			return;

		self::$mp->createAlias($oldId, $newId);
	}
}
