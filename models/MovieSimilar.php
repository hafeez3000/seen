<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

use \app\models\Movie;

/**
 * This is the model class for Similar Movies.
 *
 * @property string $id
 * @property string $movie_id
 * @property string $similar_to_movie_id
 *
 * @property Movie $similarToMovie
 * @property Movie $movie
 */
class MovieSimilar extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%movie_similar}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['movie_id', 'similar_to_movie_id'], 'required'],
			[['movie_id', 'similar_to_movie_id'], 'integer']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Movie/Similar', 'ID'),
			'movie_id' => Yii::t('Movie/Similar', 'Movie'),
			'similar_to_movie_id' => Yii::t('Movie/Similar', 'Similiar Movie'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getSimilarToMovie()
	{
		return $this->hasOne(Movie::className(), ['id' => 'similar_to_movie_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getMovie()
	{
		return $this->hasOne(Movie::className(), ['id' => 'movie_id']);
	}
}
