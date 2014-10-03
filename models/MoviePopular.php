<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;

/**
 * This is the model class for popular Movies.
 *
 * @property integer $id
 * @property integer $movie_id
 * @property integer $order
 * @property string $created_at
 *
 * @property Movie $movie
 */
class MoviePopular extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%movie_popular}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['movie_id', 'order'], 'required'],
			[['movie_id', 'order'], 'integer'],
			[['created_at'], 'date', 'format' => 'php:Y-m-d H:i:s']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Movie/Popular', 'ID'),
			'movie_id' => Yii::t('Movie/Popular', 'Movie'),
			'order' => Yii::t('Movie/Popular', 'Order'),
			'created_at' => Yii::t('Movie/Popular', 'Created at'),
		];
	}

	public function behaviors()
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::className(),
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
				],
			],
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
