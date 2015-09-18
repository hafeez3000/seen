<?php namespace app\components;

use \Yii;
use \Mixpanel;

class YiiMixpanel
{
	protected static $mp = null;

	/**
	 * Check if current user is eventually a bot.
	 *
	 * @return boolean
	 */
	protected static function isBot()
	{
		if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'])) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Init the mixpanel component
	 *
	 * @return [type]
	 */
	protected static function init()
	{
		if (isset(Yii::$app->params['mixpanel']) && !self::isBot() && self::$mp === null)
			self::$mp =  Mixpanel::getInstance(Yii::$app->params['mixpanel']);
		else
			self::$mp = null;
	}

	/**
	 * Track an event.
	 *
	 * @param string $event
	 * @param array $data
	 *
	 * @return void
	 */
	public static function track($event, array $data = array())
	{
		self::init();
		if (self::$mp === null)
			return;

		self::$mp->track($event, $data);
	}

	/**
	 * Register global variables.
	 *
	 * @param string $property
	 * @param array $data
	 *
	 * @return void
	 */
	public static function register($property, array $data)
	{
		self::init();
		if (self::$mp === null)
			return;

		self::$mp->registerAll($property, $data);
	}

	/**
	 * Identify a user.
	 *
	 * @param string $id
	 *
	 * @return void
	 */
	public static function identify($id)
	{
		self::init();
		if (self::$mp === null)
			return;

		self::$mp->identify($id);
	}

	/**
	 * Connect to user accounts.
	 *
	 * @param string $oldId
	 * @param string $newId
	 *
	 * @return void
	 */
	public static function createAlias($oldId, $newId)
	{
		self::init();
		if (self::$mp === null)
			return;

		self::$mp->createAlias($oldId, $newId);
	}
}
