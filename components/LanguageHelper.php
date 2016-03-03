<?php namespace app\components;

use \Yii;
use \yii\helpers\Html;

use \app\models\Language;

class LanguageHelper
{
	protected static function format($timestamp, $format)
	{
		$date = new \DateTime;
		$date->setTimezone(new \DateTimeZone('UTC'));
		$date->setTimestamp($timestamp);

		if (!Yii::$app->user->isGuest)
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

	public static function number($number, $decimals = 0)
	{
		if (isset(Yii::$app->params['lang'][Yii::$app->language]['decPoint']))
			$decPoint = Yii::$app->params['lang'][Yii::$app->language]['decPoint'];
		else
			$decPoint = Yii::$app->params['lang'][Yii::$app->params['lang']['default']]['decPoint'];

		if (isset(Yii::$app->params['lang'][Yii::$app->language]['thousandsSep']))
			$thousandsSep = Yii::$app->params['lang'][Yii::$app->language]['thousandsSep'];
		else
			$thousandsSep = Yii::$app->params['lang'][Yii::$app->params['lang']['default']]['thousandsSep'];

		return number_format($number, $decimals, $decPoint, $thousandsSep);
	}

	public static function navigation()
	{
		$currentIso = substr(Yii::$app->language, 0, 2);

		$currentLanguage = Language::find()
			->where(['iso' => $currentIso])
			->select('iso')
			->asArray()
			->one();

		$languages = Language::find()
			->select(['iso', 'name'])
			->where(['hide' => false])
			->asArray()
			->orderBy(['name' => SORT_ASC])
			->all();

		$items = [];
		foreach ($languages as $language) {
			$items += [
				$language['iso'] => $language['name']
			];
		}

		return '<li id="language-selector-wrapper">' . Html::dropDownList('language', $currentLanguage['iso'], $items, [
			'id' => 'language-selector',
		]) . '</li>';
	}
}
