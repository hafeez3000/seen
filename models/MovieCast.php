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
 * @property integer $person_id
 * @property string $credit_id
 * @property string $character
 * @property integer $order
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Movie $movie
 * @property Person $person
 */
class MovieCast extends ActiveRecord
{
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
			[['movie_id', 'person_id'], 'required'],
			[['id', 'movie_id', 'person_id', 'order'], 'integer'],
			[['created_at', 'updated_at'], 'date', 'format' => 'Y-m-d H:i:s'],
			[['character'], 'string', 'max' => 255],
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
			'movie_id' => Yii::t('Movie/Cast', 'Movie'),
			'person_id' => Yii::t('Movie/Cast', 'Person'),
			'credit_id' => Yii::t('Movie/Cast', 'Credit ID'),
			'character' => Yii::t('Movie/Cast', 'Character'),
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

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPerson()
	{
		return $this->hasOne(Person::className(), ['id' => 'person_id']);
	}
}
