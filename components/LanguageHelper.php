<?php namespace app\components;

use \Yii;
use \yii\helpers\Url;

use \app\models\Language;

class LanguageHelper
{
	protected static function format($timestamp, $format)
	{
		$date = new \DateTime;
		$date->setTimezone(new \DateTimeZone('UTC'));
		$date->setTimestamp($timestamp);
		$date->setTimezone(new \DateTimeZone(Yii::$app->user->identity->timezone));

		return $date->format($format);
	}

	public static function dateTime($timestamp)
	{
		if (isset(Yii::$app->params['lang'][Yii::$app->language]['datetime']))
			$format = Yii::$app->params['lang'][Yii::$app->language]['datetime'];
		else
			$format = Yii::$app->params['lang'][Yii::$app->params['lang']['default']]['datetime'];

		return self::format($timestamp, $format);
	}

	public static function date($timestamp)
	{
		if (isset(Yii::$app->params['lang'][Yii::$app->language]['date']))
			$format = Yii::$app->params['lang'][Yii::$app->language]['date'];
		else
			$format = Yii::$app->params['lang'][Yii::$app->params['lang']['default']]['date'];

		return self::format($timestamp, $format);
	}

	public static function navigation()
	{
		$currentLanguage = Language::find()
			->where(['iso' => Yii::$app->language])
			->select(['name'])
			->asArray()
			->one();

		$return  = [
			'label' => $currentLanguage['name'],
			'items' => [],
		];
		$languages = Language::find()
			->select(['iso', 'name'])
			->asArray()
			->orderBy(['name' => SORT_ASC])
			->all();

		foreach ($languages as $language) {
			$return['items'][] = [
				'label' => $language['name'],
				'url' => Url::toRoute(['site/language', 'iso' => $language['iso']])
			];
		}

		return $return;
	}
}