<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;

/**
 * This is the model class for Companies.
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $parent_id
 * @property string $headquarters
 * @property string $homepage
 * @property string $logo_path
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property Movie[] $movies
 */
class Company extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%company}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['description'], 'string'],
			[['parent_id'], 'integer'],
			[['created_at', 'updated_at', 'deleted_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
			[['name', 'headquarters', 'homepage', 'logo_path'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Company', 'ID'),
			'name' => Yii::t('Company', 'Name'),
			'description' => Yii::t('Company', 'Description'),
			'parent_id' => Yii::t('Company', 'Parent Company'),
			'headquarters' => Yii::t('Company', 'Headquarter'),
			'homepage' => Yii::t('Company', 'Homepage'),
			'logo_path' => Yii::t('Company', 'Logo path'),
			'created_at' => Yii::t('Company', 'Created at'),
			'updated_at' => Yii::t('Company', 'Updated at'),
			'deleted_at' => Yii::t('Company', 'Deleted at'),
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
		return $this->hasMany(Movie::className(), ['id' => 'movie_id'])->viaTable('prod_movie_company', ['company_id' => 'id']);
	}
}
