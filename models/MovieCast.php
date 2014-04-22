<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;
use \app\components\PersonTrait;

/**
 * This is the model class for the Movie Cast.
 *
 * @property integer $id
 * @property integer $movie_id
 * @property string $credit_id
 * @property string $name
 * @property string $character
 * @property string $profile_path
 * @property integer $order
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Movie $movie
 */
class MovieCast extends ActiveRecord
{
	use PersonTrait;

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%movie_cast}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id', 'movie_id'], 'required'],
			[['id', 'movie_id', 'order'], 'integer'],
			[['created_at', 'updated_at'], 'date', 'format' => 'Y-m-d H:i:s'],
			[['name', 'character', 'profile_path'], 'string', 'max' => 255],
			[['credit_id'], 'string', 'max' => 50],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Movie/Cast', 'ID'),
			'movie_id' => Yii::t('Movie/Cast', 'Movie ID'),
			'credit_id' => Yii::t('Movie/Cast', 'Credit ID'),
			'name' => Yii::t('Movie/Cast', 'Name'),
			'character' => Yii::t('Movie/Cast', 'Character'),
			'profile_path' => Yii::t('Movie/Cast', 'Profile path'),
			'order' => Yii::t('Movie/Cast', 'Order'),
			'created_at' => Yii::t('Movie/Cast', 'Created at'),
			'updated_at' => Yii::t('Movie/Cast', 'Updated at'),
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
