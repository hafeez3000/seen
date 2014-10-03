<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;

/**
 * This is the model class for Movies seen by User.
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $movie_id
 * @property string $created_at
 *
 * @property Movie $movie
 */
class UserMovie extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%user_movie}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['user_id', 'movie_id'], 'required'],
			[['user_id', 'movie_id'], 'integer'],
			[['created_at'], 'date', 'format' => 'php:Y-m-d H:i:s']
		];
	}

	/**
	 * @inheritdoc
	 */
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
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('User/Movie', 'ID'),
			'user_id' => Yii::t('User/Movie', 'User'),
			'movie_id' => Yii::t('User/Movie', 'Movie'),
			'created_at' => Yii::t('User/Movie', 'Created at'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getMovie()
	{
		return $this->hasOne(Movie::className(), ['id' => 'movie_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}
}
