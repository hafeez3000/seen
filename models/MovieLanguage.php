<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for Movie Languages.
 *
 * @property integer $movie_id
 * @property integer $language_id
 *
 * @property Language $language
 * @property Movie $movie
 */
class MovieLanguage extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%movie_language}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['movie_id', 'language_id'], 'required'],
			[['movie_id', 'language_id'], 'integer']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'movie_id' => Yii::t('Movie/Language', 'Movie'),
			'language_id' => Yii::t('Movie/Language', 'Language'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getLanguage()
	{
		return $this->hasOne(Language::className(), ['id' => 'language_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getMovie()
	{
		return $this->hasOne(Movie::className(), ['id' => 'movie_id']);
	}
}
