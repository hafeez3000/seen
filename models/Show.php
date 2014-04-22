<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

use \app\components\TimestampBehavior;

/**
 * This is the model class for TV Shows.
 *
 * @property integer $id
 * @property integer $language_id
 * @property string $name
 * @property string $original_name
 * @property string $slug
 * @property string $overview
 * @property string $homepage
 * @property string $first_air_date
 * @property string $last_air_date
 * @property boolean $in_production
 * @property double $popularity
 * @property string $backdrop_path
 * @property string $poster_path
 * @property string $status
 * @property double $vote_average
 * @property integer $vote_count
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property Season[] $seasons
 * @property Language $language
 * @property Country[] $countries
 * @property Peroson[] $creators
 * @property Genre[] $genres
 * @property Network[] $networks
 * @property ShowRuntime[] $runtimes
 * @property User[] $users
 * @property ShowCast[] $cast
 * @property ShowCrew[] $crew
 */
class Show extends ActiveRecord
{
	private $isUserSubscribedCache = [];

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%show}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['themoviedb_id', 'language_id'], 'required'],
			[['themoviedb_id', 'language_id', 'vote_count'], 'integer'],
			[['in_production'], 'boolean'],
			[['overview'], 'string'],
			[['first_air_date', 'last_air_date'], 'date', 'format' => 'Y-m-d'],
			[['created_at', 'updated_at', 'deleted_at'], 'date', 'format' => 'Y-m-d H:i:s'],
			[['popularity', 'vote_average'], 'number'],
			[['name', 'original_name', 'slug', 'homepage', 'backdrop_path', 'poster_path'], 'string', 'max' => 255],
			[['status'], 'string', 'max' => 100]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Show', 'ID'),
			'themoviedb_id' => Yii::t('Show', 'TheMovieDB'),
			'language_id' => Yii::t('Show', 'Language'),
			'name' => Yii::t('Show', 'Name'),
			'original_name' => Yii::t('Show', 'Original name'),
			'slug' => Yii::t('Show', 'Slug'),
			'overview' => Yii::t('Show', 'Overview'),
			'homepage' => Yii::t('Show', 'Homepage'),
			'first_air_date' => Yii::t('Show', 'First air date'),
			'last_air_date' => Yii::t('Show', 'Last air date'),
			'in_production' => Yii::t('Show', 'In production'),
			'popularity' => Yii::t('Show', 'Popularity'),
			'backdrop_path' => Yii::t('Show', 'Backdrop path'),
			'poster_path' => Yii::t('Show', 'Poster path'),
			'status' => Yii::t('Show', 'Staus'),
			'vote_average' => Yii::t('Show', 'Average vote'),
			'vote_count' => Yii::t('Show', 'Vote count'),
			'created_at' => Yii::t('Show', 'Created at'),
			'updated_at' => Yii::t('Show', 'Updated at'),
			'deleted_at' => Yii::t('Show', 'Deleted at'),
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
			'slug' => [
				'class' => 'Zelenin\yii\behaviors\Slug',
				'source_attribute' => ['name', 'language.iso'],
				'slug_attribute' => 'slug',
				'replacement' => '-',
				'unique' => true,
			],
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getSeasons()
	{
		return $this->hasMany(Season::className(), ['show_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getLanguage()
	{
		return $this->hasOne(Language::className(), ['id' => 'language_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCountries()
	{
		return $this->hasMany(Country::className(), ['id' => 'country_id'])->viaTable('{{%show_country}}', ['show_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCreators()
	{
		return $this->hasMany(Person::className(), ['id' => 'person_id'])->viaTable('{{%show_creator}}', ['show_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getGenres()
	{
		return $this->hasMany(Genre::className(), ['id' => 'genre_id'])->viaTable('{{%show_genre}}', ['show_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getNetworks()
	{
		return $this->hasMany(Network::className(), ['id' => 'network_id'])->viaTable('{{%show_network}}', ['show_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRuntimes()
	{
		return $this->hasMany(ShowRuntime::className(), ['show_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUsers()
	{
		return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('{{%user_show}}', ['id' => 'show_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCast()
	{
		return $this->hasMany(ShowCast::className(), ['show_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCrew()
	{
		return $this->hasMany(ShowCrew::className(), ['show_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUserShow()
	{
		return $this->hasMany(UserShow::className(), ['show_id' => 'id']);
	}

	/**
	 * Check if the current user is subscribed to the show.
	 *
	 * @return boolean
	 */
	public function getIsUserSubscribed()
	{
		if (Yii::$app->user->isGuest)
			return false;

		if (!isset($this->isUserSubscribedCache[Yii::$app->user->id])) {
			$isSubscribed = $this->getUserShow()
				->where(['user_id' => Yii::$app->user->id])
				->andWhere(['deleted_at' => null])
				->exists();

			$this->isUserSubscribedCache[Yii::$app->user->id] = $isSubscribed;
		}

		return $this->isUserSubscribedCache[Yii::$app->user->id];
	}

	/**
	 * @return UserEpisode|null
	 */
	public function getLastEpisode()
	{
		return UserEpisode::findBySql('
			SELECT
				{{%user_episode}}.*
			FROM
				{{%user_episode}},
				{{%user_show_run}},
				{{%episode}},
				{{%season}},
				{{%show}}
			WHERE
				{{%user_show_run}}.[[user_id]] = :user_id AND
				{{%user_episode}}.[[run_id]] = {{%user_show_run}}.[[id]] AND
				{{%user_episode}}.[[episode_id]] = {{%episode}}.[[id]] AND
				{{%episode}}.[[season_id]] = {{%season}}.[[id]] AND
				{{%season}}.[[show_id]] = {{%show}}.[[id]] AND
				{{%show}}.[[id]] = :id
			ORDER BY
				{{%user_episode}}.[[created_at]] DESC
			LIMIT
				1
		')
			->addParams([
				':id' => $this->id,
				':user_id' => Yii::$app->user->id,
			]);
	}

	public function getBackdropUrl()
	{
		if (!empty($this->backdrop_path))
			return Yii::$app->params['themoviedb']['image_url'] . 'w1280/' . $this->backdrop_path;
		else
			return 'http://placehold.it/1280x300/fff/555&' . http_build_query(['text' => $this->name]);
	}

	public function getPosterUrl()
	{
		if (!empty($this->poster_path))
			return Yii::$app->params['themoviedb']['image_url'] . 'w185/' . $this->poster_path;
		else
			return 'http://placehold.it/175x272/fff/555&' . http_build_query(['text' => $this->name]);
	}

	public function getPosterLargeUrl()
	{
		if (!empty($this->poster_path))
			return Yii::$app->params['themoviedb']['image_url'] . 'w500/' . $this->poster_path;
		else
			return 'http://placehold.it/300x169/fff/555&' . http_build_query(['text' => $this->name]);
	}
}
