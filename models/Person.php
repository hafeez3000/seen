<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;

/**
 * This is the model class for table "prod_person".
 *
 * @property integer $id
 * @property string $name
 * @property string $biography
 * @property string $birthday
 * @property string $deathday
 * @property string $homepage
 * @property boolean $adult
 * @property string $place_of_birth
 * @property string $profile_path
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property PersonAlias[] $aliases
 * @property Show[] $shows
 */
class Person extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%person}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['biography'], 'string'],
			[['birthday', 'deathday'], 'date', 'format' => 'Y-m-d'],
			[['created_at', 'updated_at', 'deleted_at'], 'date', 'format' => 'Y-m-d H:i:s'],
			[['adult'], 'boolean'],
			[['name', 'homepage', 'place_of_birth', 'profile_path'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Person', 'ID'),
			'name' => Yii::t('Person', 'Name'),
			'biography' => Yii::t('Person', 'Biography'),
			'birthday' => Yii::t('Person', 'Birthday'),
			'deathday' => Yii::t('Person', 'Deathday'),
			'homepage' => Yii::t('Person', 'Homepage'),
			'adult' => Yii::t('Person', 'Adult'),
			'place_of_birth' => Yii::t('Person', 'Place of birth'),
			'profile_path' => Yii::t('Person', 'Profile path'),
			'created_at' => Yii::t('Person', 'Created at'),
			'updated_at' => Yii::t('Person', 'Updated at'),
			'deleted_at' => Yii::t('Person', 'Deleted at'),
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
	public function getAliases()
	{
		return $this->hasMany(PersonAlias::className(), ['person_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getShows()
	{
		return $this->hasMany(Show::className(), ['id' => 'show_id'])->viaTable('prod_show_created', ['person_id' => 'id']);
	}
}
