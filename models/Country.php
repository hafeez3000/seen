<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;

/**
 * This is the model class for Countries.
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
class Country extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%country}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['name'], 'required'],
			[['created_at', 'updated_at', 'deleted_at'], 'date', 'format' => 'Y-m-d H:i:s'],
			[['name'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Country', 'ID'),
			'name' => Yii::t('Country', 'Name'),
			'created_at' => Yii::t('Country', 'Created at'),
			'updated_at' => Yii::t('Country', 'Updated at'),
			'deleted_at' => Yii::t('Country', 'Deleted at'),
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
		return $this->hasMany(Movie::className(), ['id' => 'movie_id'])->viaTable('prod_movie_country', ['country_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getShows()
	{
		return $this->hasMany(Show::className(), ['id' => 'show_id'])->viaTable('prod_show_country', ['country_id' => 'id']);
	}
}
