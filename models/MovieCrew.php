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
 * @property string $department
 * @property string $job
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Movie $movie
 */
class MovieCrew extends ActiveRecord
{
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
			[['movie_id', 'person_id'], 'required'],
			[['id', 'movie_id', 'person_id'], 'integer'],
			[['created_at', 'updated_at'], 'date', 'format' => 'Y-m-d H:i:s'],
			[['department', 'job'], 'string', 'max' => 255],
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
			'movie_id' => Yii::t('Movie/Crew', 'Movie'),
			'person_id' => Yii::t('Movie/Crew', 'Person'),
			'credit_id' => Yii::t('Movie/Crew', 'Credit ID'),
			'department' => Yii::t('Movie/Crew', 'Department'),
			'job' => Yii::t('Movie/Crew', 'Job'),
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

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPerson()
	{
		return $this->hasOne(Person::className(), ['id' => 'person_id']);
	}
}
