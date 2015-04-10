<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

use \predictionio\EngineClient;

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
 * @property UserShow[] $userShows
 * @property Show $popularShows
 * @property ShowVideo[] $videos
 */
class Show extends ActiveRecord
{
	private $isUserSubscribedCache = [];

	private static $_lastUserEpisodeCache= [];

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
			[['first_air_date', 'last_air_date'], 'date', 'format' => 'php:Y-m-d'],
			[['created_at', 'updated_at', 'deleted_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
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

	/**
	 * @inheritdoc
	 */
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
				'attribute' => ['themoviedb_id', 'completeName', 'language.iso'],
				'slugAttribute' => 'slug',
				'replacement' => '-',
				'lowercase' => true,
				'ensureUnique' => true,
				'immutable' => false,
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function fields()
	{
		return [
			'id' => 'themoviedb_id',
			'language' => function() {
				return $this->language->iso;
			},
			'name',
			'original_name',
			'overview',
			'homepage',
			'first_air_date',
			'last_air_date',
			'in_production',
			'popularity',
			'backdrop_path',
			'poster_path',
			'status',
			'vote_average',
			'vote_count',
			'last_update' => 'updated_at',
			'seasons',
		];
	}

	public static function popular($languageId)
	{
		return self::findBySql('
			SELECT DISTINCT
				{{%show}}.*
			FROM
				{{%show}},
				{{%show_popular}}
			WHERE
				{{%show}}.[[language_id]] = :language_id AND
				{{%show}}.[[id]] = {{%show_popular}}.[[show_id]] AND
				{{%show}}.[[name]] != ""
			ORDER BY
				{{%show_popular}}.[[order]] ASC
		', [
			':language_id' => $languageId,
		]);
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
	public function getUserShows()
	{
		return $this->hasMany(UserShow::className(), ['show_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPopularShows()
	{
		return $this->hasMany(Show::className(), ['id' => 'show_id'])->viaTable('{{%show_popular}}', ['show_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getVideos()
	{
		return $this->hasMany(ShowVideo::className(), ['show_id' => 'id']);
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
			$isSubscribed = $this->getUserShows()
				->where(['user_id' => Yii::$app->user->id])
				->andWhere(['deleted_at' => null])
				->exists();

			$this->isUserSubscribedCache[Yii::$app->user->id] = $isSubscribed;
		}

		return $this->isUserSubscribedCache[Yii::$app->user->id];
	}

	/**
	 * Get the original show name if the localized one is the same
	 * and therefore empty.
	 *
	 * @return string
	 */
	public function getCompleteName()
	{
		if (!empty($this->name))
			return $this->name;
		else
			return $this->original_name;
	}

	/**
	 * Get the latest episode for all user shows and cache the result.
	 *
	 * @param mixed $userId null|int
	 * @param array $shows
	 *
	 * @return void
	 */
	public static function warmLatestEpisodeCache($userId = null, $shows)
	{
		$userId = ($userId === null) ? Yii::$app->user->id : $userId;

		$ids = array_map(function($show) {
			return (int) $show->id;
		}, $shows);

		if (count($ids) === 0)
			return;

		$episodes = UserEpisode::findBySql('
			SELECT
				{{ue1}}.*
			FROM
				{{%user_episode}} AS {{ue1}}
			INNER JOIN (
				SELECT
					{{%user_show_run}}.[[show_id]],
					MAX({{%user_episode}}.[[created_at]]) AS [[created_at]]
				FROM
					{{%user_episode}},
					{{%user_show_run}},
					{{%episode}},
					{{%season}}
				WHERE
					{{%user_show_run}}.[[user_id]] = :userId AND
					{{%user_episode}}.[[run_id]] = {{%user_show_run}}.[[id]] AND
					{{%user_episode}}.[[episode_id]] = {{%episode}}.[[id]] AND
					{{%episode}}.[[season_id]] = {{%season}}.[[id]] AND
					{{%season}}.[[show_id]] IN (' . implode(',', $ids) /* TODO: Add parameter */ . ')
				GROUP BY
					{{%user_show_run}}.[[show_id]]
			) AS {{ue2}} ON (
				{{ue1}}.[[created_at]] = {{ue2}}.[[created_at]] AND
				{{ue1}}.[[id]] IN (
					SELECT
						{{%user_episode}}.[[id]]
					FROM
						{{%user_episode}},
						{{%user_show_run}}
					WHERE
						{{%user_show_run}}.[[user_id]] = :userId AND
						{{%user_episode}}.[[run_id]] = {{%user_show_run}}.[[id]]
				)
			)
			')
			->with('run')
			->addParams([
				':userId' => $userId,
			])
			->all();

		foreach ($episodes as $episode) {
			self::$_lastUserEpisodeCache[$userId][$episode->run->show_id] = $episode;
		}

		foreach ($shows as $show) {
			if (!isset(self::$_lastUserEpisodeCache[$userId][$show->id]))
				self::$_lastUserEpisodeCache[$userId][$show->id] = null;
		}
	}

	/**
	 * @return UserEpisode|null
	 */
	public function getLastEpisode($userId = null, $cache = false)
	{
		$userId = ($userId === null) ? Yii::$app->user->id : $userId;

		if ($cache && isset(self::$_lastUserEpisodeCache[$userId]) && array_key_exists($this->id, self::$_lastUserEpisodeCache[$userId]))
			return self::$_lastUserEpisodeCache[$userId][$this->id];

		$episode = UserEpisode::find()
			->select('{{%user_episode}}.*')
			->from([
				'{{%user_episode}}',
				'{{%user_show_run}}',
				'{{%episode}}',
				'{{%season}}',
			])
			->where([
				'{{%user_show_run}}.user_id' => $userId,
			])
			->andWhere([
				'and',
				'{{%user_episode}}.run_id = {{%user_show_run}}.id',
				'{{%user_episode}}.episode_id = {{%episode}}.id',
				'{{%episode}}.season_id = {{%season}}.id'
			])
			->andWhere([
				'{{%season}}.show_id' => $this->id,
			])
			->groupBy('{{%user_show_run}}.show_id')
			->orderBy(['{{%user_episode}}.created_at' => SORT_DESC])
			->limit(1)
			->one();

		self::$_lastUserEpisodeCache[$userId][$this->id] = $episode;

		return $episode;
	}

	/**
	 * Get all seen episodes for the current run.
	 *
	 * @param integer $id User ID. If null, the current user is used
	 *
	 * @return UserEpisode[]
	 */
	public function getLastEpisodes($id = null)
	{
		return UserEpisode::findBySql('
			SELECT DISTINCT
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
				{{%show}}.[[id]] = :show_id
			ORDER BY
				{{%season}}.[[number]] DESC,
				{{%episode}}.[[number]] DESC
		')
			->addParams([
				':show_id' => $this->id,
				':user_id' => ($id === null) ? Yii::$app->user->id : $id,
			]);
	}

	/**
	 * Get all seen episodes for this show.
	 *
	 * @param integer $id User ID. If null, the current user is used
	 *
	 * @return UserEpisode[]
	 */
	public function getAllUserEpisodes($id = null)
	{
		return UserEpisode::find()
			->select(UserEpisode::tableName() . '.*')
			->distinct()
			->from([
				UserEpisode::tableName(),
				UserShowRun::tableName(),
			])
			->where(['{{%user_show_run}}.[[show_id]]' => $this->id])
			->andWhere(['{{%user_show_run}}.[[user_id]]' => ($id === null) ? Yii::$app->user->id : $id])
			->andWhere('{{%user_episode}}.[[run_id]] = {{%user_show_run}}.[[id]]');
	}

	/**
	 * Get the latest episodes for the current user seen in this show.
	 *
	 * @return yii\db\ActiveQuery
	 */
	public function getLatestUserEpisodes()
	{
		return UserEpisode::findBySql('
			SELECT DISTINCT
				{{%user_episode}}.*
			FROM
				{{%episode}},
				{{%user_episode}},
				{{%season}}
			WHERE
				{{%season}}.[[show_id]] = :show_id AND
				{{%episode}}.[[season_id]] = {{%season}}.[[id]] AND
				{{%episode}}.[[id]] = {{%user_episode}}.[[episode_id]] AND
				{{%user_episode}}.[[run_id]] = (
					SELECT
						{{%user_show_run}}.[[id]]
					FROM
						{{%user_show_run}}
					WHERE
						{{%user_show_run}}.[[user_id]] = :user_id AND
						{{%user_show_run}}.[[show_id]] = :show_id
					ORDER BY
						{{%user_show_run}}.[[created_at]] DESC
					LIMIT 1
				)
		')
			->addParams([
				':user_id' => Yii::$app->user->id,
				':show_id' => $this->id,
			]);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public static function getRecommend()
	{
		return [];

		// ToDo: Implement PredictionIO
		try {
			$client = new EngineClient(Yii::$app->params['prediction']['engineserver']);

			$showIds = $client->sendQuery([
				'user' => Yii::$app->user->id,
				'num' => 50,
				'item' => 'movie',
			])['itemScores'];

			$showIds = array_map(function($item) {
				return (int) str_replace('tv-', '', $item['item']);
			}, $showIds);

			$query = Show::find()
				->distinct()
				->select('{{%show}}.*')
				->from([
					'{{%show}}',
					'{{%language}}',
				])
				->where(['in', '{{%show}}.[[themoviedb_id]]', $showIds])
				->andWhere('{{%show}}.[[language_id]] = {{%language}}.[[id]]')
				->andWhere('{{%language}}.[[iso]] = :language')
				->params([
					':language' => Yii::$app->language,
				]);

			return $query;
		} catch (\Exception $e) {
			Yii::error('Error while getting user movie predictions:' . $e->getMessage());

			return Show::find()->where(['id' => 0]);
		}
	}

	/**
	 * Get an array for the latest episodes seen by the current user indexed by
	 * episode ids
	 *
	 * @return array
	 */
	public function getUserEpisodesSeen()
	{
		return $this
			->getLatestUserEpisodes()
			->indexBy('episode_id')
			->asArray()
			->all();
	}

	/**
	 * Get the backdrop image.
	 *
	 * @return string
	 */
	public function getBackdropUrl()
	{
		if (!empty($this->backdrop_path))
			return 'src="' . Yii::$app->params['themoviedb']['image_url'] . 'w780' . $this->backdrop_path . '"';
		else
			return 'data-src="holder.js/720x720/#eee:#555/text:' . $this->name . '"';
	}

	/**
	 * Get the poster image.
	 *
	 * @return string
	 */
	public function getPosterUrl()
	{
		if (!empty($this->poster_path))
			return 'src="' . Yii::$app->params['themoviedb']['image_url'] . 'w185' . $this->poster_path . '"';
		else
			return 'data-src="holder.js/175x272/#eee:#555/text:' . $this->name . '"';
	}

	/**
	 * Get the large poster image.
	 *
	 * @return string
	 */
	public function getPosterUrlLarge()
	{
		if (!empty($this->poster_path))
			return 'src="' . Yii::$app->params['themoviedb']['image_url'] . 'w500' . $this->poster_path . '"';
		else
			return 'data-src="holder.js/500x735/#eee:#555/text:' . $this->name . '"';
	}

	/**
	 * Check if the show is in the archive of the user with $userId or the current
	 * user if $userId is null.
	 *
	 * @param mixed $userId User ID (Default: null)
	 * @return boolean
	 */
	public function isArchived($userId = null)
	{
		$userId = ($userId === null) ? Yii::$app->user->id : $userId;

		$userShow = UserShow::find()
			->where(['[[show_id]]' => $this->id])
			->andWhere(['[[user_id]]' => $userId])
			->one();

		return (bool) $userShow['archived'];
	}
}
