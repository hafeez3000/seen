<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;
use \app\components\PersonTrait;

/**
 * This is the model class for the Movie Crew.
 *
 * @property integer $id
 * @property integer $movie_id
 * @property string $credit_id
 * @property string $name
 * @property string $department
 * @property string $job
 * @property string $profile_path
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Movie $movie
 */
class MovieCrew extends ActiveRecord
{
	use PersonTrait;

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%movie_crew}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id', 'movie_id'], 'required'],
			[['id', 'movie_id'], 'integer'],
			[['created_at', 'updated_at'], 'date', 'format' => 'Y-m-d H:i:s'],
			[['name', 'department', 'job', 'profile_path'], 'string', 'max' => 255],
			[['credit_id'], 'string', 'max' => 50],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Movie/Crew', 'ID'),
			'movie_id' => Yii::t('Movie/Crew', 'Movie ID'),
			'credit_id' => Yii::t('Movie/Crew', 'Credit ID'),
			'name' => Yii::t('Movie/Crew', 'Name'),
			'department' => Yii::t('Movie/Crew', 'Department'),
			'job' => Yii::t('Movie/Crew', 'Job'),
			'profile_path' => Yii::t('Movie/Crew', 'Profile path'),
			'created_at' => Yii::t('Movie/Crew', 'Created at'),
			'updated_at' => Yii::t('Movie/Crew', 'Updated at'),
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
