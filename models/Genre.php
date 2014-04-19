<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;


/**
 * This is the model class for Genres.
 *
 * @property integer $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property Movie[] $movies
 * @property Show[] $shows
 */
class Genre extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%genre}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['name'], 'required'],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
			[['name'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Genre', 'ID'),
			'name' => Yii::t('Genre', 'Name'),
			'created_at' => Yii::t('Genre', 'Created at'),
			'updated_at' => Yii::t('Genre', 'Updated at'),
			'deleted_at' => Yii::t('Genre', 'Deleted at'),
		];
	}

	public function behaviors()
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::className(),
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
				],
			],
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getMovies()
	{
		return $this->hasMany(Movie::className(), ['id' => 'movie_id'])->viaTable('prod_movie_genre', ['genre_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getShows()
	{
		return $this->hasMany(Show::className(), ['id' => 'show_id'])->viaTable('prod_show_genre', ['genre_id' => 'id']);
	}
}
