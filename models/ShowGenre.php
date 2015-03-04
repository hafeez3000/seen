<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table Show Genres.
 *
 * @property integer $show_id
 * @property integer $genre_id
 *
 * @property Genre $genre
 * @property Show $show
 */
class ShowGenre extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%show_genre}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['show_id', 'genre_id'], 'required'],
			[['show_id', 'genre_id'], 'integer']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'show_id' => Yii::t('Show/Genre', 'Show'),
			'genre_id' => Yii::t('Show/Genre', 'Genre'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getGenre()
	{
		return $this->hasOne(Genre::className(), ['id' => 'genre_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getShow()
	{
		return $this->hasOne(Show::className(), ['id' => 'show_id']);
	}
}
