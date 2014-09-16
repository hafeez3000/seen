<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class movie videos.
 *
 * @property string $id
 * @property integer $movie_id
 * @property string $key
 * @property string $name
 * @property string $site
 * @property integer $size
 * @property string $type
 *
 * @property Movie $movie
 */
class MovieVideo extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%movie_video}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id', 'movie_id'], 'required'],
			[['movie_id', 'size'], 'integer'],
			[['id'], 'string', 'max' => 32],
			[['key', 'name'], 'string', 'max' => 255],
			[['site'], 'string', 'max' => 127],
			[['type'], 'string', 'max' => 31]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Movie/Video', 'Video ID'),
			'movie_id' => Yii::t('Movie/Video', 'Movie'),
			'key' => Yii::t('Movie/Video', 'Key'),
			'name' => Yii::t('Movie/Video', 'Name'),
			'site' => Yii::t('Movie/Video', 'Site'),
			'size' => Yii::t('Movie/Video', 'Size'),
			'type' => Yii::t('Movie/Video', 'Type'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getMovie()
	{
		return $this->hasOne(Movie::className(), ['id' => 'movie_id']);
	}
}
