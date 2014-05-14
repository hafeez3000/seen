<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;
use \app\components\PersonTrait;

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
			[['id'], 'required'],
			[['id'], 'integer'],
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
	 * Get the absolute url to the profile image.
	 *
	 * @return string
	 */
	public function getProfileUrl()
	{
		if (!empty($this->profile_path))
			return 'src="' . Yii::$app->params['themoviedb']['image_url'] . 'w45' . $this->profile_path . '"';
		else
			return 'data-src="holder.js/45x68/#eee:#555/text:' . $this->name . '"';
	}

	/**
	 * Get the absolute url to the large profile image.
	 *
	 * @return string
	 */
	public function getProfileUrlLarge()
	{
		if (!empty($this->profile_path))
			return 'src="' . Yii::$app->params['themoviedb']['image_url'] . 'w185' . $this->profile_path . '"';
		else
			return 'data-src="holder.js/165x248/#eee:#555/text:' . $this->name . '"';
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAliases()
	{
		return $this->hasMany(PersonAlias::className(), ['person_id' => 'id']);
	}

	public function getShows()
	{
		$language = Language::find()
			->where(['iso' => Yii::$app->language])
			->one();

		return Show::findBySql('
			SELECT DISTINCT
				{{%show}}.*
			FROM
				{{%show}}
			WHERE
				{{%show}}.[[language_id]] = :language_id AND
				(
					{{%show}}.[[id]] IN (
						SELECT DISTINCT
							{{show_cast}}.[[show_id]]
						FROM
							{{%show_cast}} as {{show_cast}}
						WHERE
							{{show_cast}}.[[person_id]] = :person_id
					) OR
					{{%show}}.[[id]] IN (
						SELECT DISTINCT
							{{show_creator}}.[[show_id]]
						FROM
							{{%show_creator}} as {{show_creator}}
						WHERE
							{{show_creator}}.[[person_id]] = :person_id
					) OR
					{{%show}}.[[id]] IN (
						SELECT DISTINCT
							{{show_crew}}.[[show_id]]
						FROM
							{{%show_crew}} as {{show_crew}}
						WHERE
							{{show_crew}}.[[person_id]] = :person_id
					)
				)
			ORDER BY
				{{%show}}.[[popularity]] DESC
		', [
			':person_id' => $this->id,
			':language_id' => $language->id,
		]);
	}

	public function getMovies()
	{
		$language = Language::find()
			->where(['iso' => Yii::$app->language])
			->one();

		return Movie::findBySql('
			SELECT DISTINCT
				{{%movie}}.*
			FROM
				{{%movie}}
			WHERE
				{{%movie}}.[[language_id]] = :language_id AND
				(
					{{%movie}}.[[id]] IN (
						SELECT DISTINCT
							{{movie_cast}}.[[movie_id]]
						FROM
							{{%movie_cast}} as {{movie_cast}}
						WHERE
							{{movie_cast}}.[[person_id]] = :person_id
					) OR
					{{%movie}}.[[id]] IN (
						SELECT DISTINCT
							{{movie_crew}}.[[movie_id]]
						FROM
							{{%movie_crew}} as {{movie_crew}}
						WHERE
							{{movie_crew}}.[[person_id]] = :person_id
					)
				)
			ORDER BY
				{{%movie}}.[[popularity]] DESC
		', [
			':person_id' => $this->id,
			':language_id' => $language->id,
		]);
	}
}
