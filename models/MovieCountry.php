<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;

/**
 * This is the model class for Movie Countries.
 *
 * @property integer $movie_id
 * @property integer $country_id
 *
 * @property Country $country
 * @property Movie $movie
 */
class MovieCountry extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%movie_country}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['movie_id', 'country_id'], 'required'],
			[['movie_id', 'country_id'], 'integer']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'movie_id' => Yii::t('Movie/Country', 'Movie'),
			'country_id' => Yii::t('Movie/Country', 'Country'),
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
	public function getMovie()
	{
		return $this->hasOne(Movie::className(), ['id' => 'movie_id']);
	}
}
