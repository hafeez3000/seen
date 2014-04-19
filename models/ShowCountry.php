<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;

/**
 * This is the model class for Show Countries.
 *
 * @property integer $show_id
 * @property integer $country_id
 *
 * @property Country $country
 * @property Show $show
 */
class ShowCountry extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%show_country}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['show_id', 'country_id'], 'required'],
			[['show_id', 'country_id'], 'integer']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'show_id' => Yii::t('Show/Country', 'Show'),
			'country_id' => Yii::t('Show/Country', 'Country'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCountry()
	{
		return $this->hasOne(Country::className(), ['id' => 'country_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getShow()
	{
		return $this->hasOne(Show::className(), ['id' => 'show_id']);
	}
}
