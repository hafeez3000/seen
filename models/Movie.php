<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

use \PredictionIO\PredictionIOClient;

use \app\components\TimestampBehavior;

/**
 * This is the model class for Movies.
 *
 * @property integer $id
 * @property integer $themoviedb_id
 * @property integer $language_id
 * @property string $title
 * @property string $original_title
 * @property string $slug
 * @property string $tagline
 * @property string $overview
 * @property string $imdb_id
 * @property string $backdrop_path
 * @property string $poster_path
 * @property string $release_date
 * @property string $budget
 * @property string $revenue
 * @property integer $runtime
 * @property string $status
 * @property boolean $adult
 * @property string $homepage
 * @property double $popularity
 * @property double $vote_average
 * @property string $vote_count
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property Language $language
 * @property Company[] $companies
 * @property Country[] $countries
 * @property Genre[] $genres
 * @property Language[] $languages
 * @property Person[] $castPersons
 * @property MovieCast[] $cast
 * @property Person[] $crewPersons
 * @property MovieCrew[] $crew
 * @property Movie[] $popularMovies
 * @property UserMovie[] $userWatches
 * @property User $userWatched
 * @property User[] $users
 */
class Movie extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%movie}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['themoviedb_id', 'language_id'], 'required'],
			[['themoviedb_id', 'language_id', 'budget', 'revenue', 'runtime', 'vote_count'], 'integer'],
			[['adult'], 'boolean'],
			[['tagline', 'overview'], 'string'],
			[['release_date'], 'date', 'format' => 'Y-m-d'],
			[['created_at', 'updated_at', 'deleted_at'], 'date', 'format' => 'Y-m-d H:i:s'],
			[['popularity', 'vote_average'], 'number'],
			[['title', 'original_title', 'slug', 'backdrop_path', 'poster_path', 'status', 'homepage'], 'string', 'max' => 255],
			[['imdb_id'], 'string', 'max' => 15]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('Movie', 'ID'),
			'themoviedb_id' => Yii::t('Movie', 'themoviedb_id'),
			'language_id' => Yii::t('Movie', 'Language ID'),
			'title' => Yii::t('Movie', 'Title'),
			'original_title' => Yii::t('Movie', 'Original Title'),
			'slug' => Yii::t('Movie', 'Slug'),
			'tagline' => Yii::t('Movie', 'Tagline'),
			'overview' => Yii::t('Movie', 'Overview'),
			'imdb_id' => Yii::t('Movie', 'IMDB ID'),
			'backdrop_path' => Yii::t('Movie', 'Backdrop path'),
			'poster_path' => Yii::t('Movie', 'Poster path'),
			'release_date' => Yii::t('Movie', 'Release date'),
			'budget' => Yii::t('Movie', 'Budget'),
			'revenue' => Yii::t('Movie', 'Revenue'),
			'runtime' => Yii::t('Movie', 'Runtime'),
			'status' => Yii::t('Movie', 'Status'),
			'adult' => Yii::t('Movie', 'Adult'),
			'homepage' => Yii::t('Movie', 'Homepage'),
			'popularity' => Yii::t('Movie', 'Popularity'),
			'vote_average' => Yii::t('Movie', 'Average vote'),
			'vote_count' => Yii::t('Movie', 'Vote count'),
			'created_at' => Yii::t('Movie', 'Created at'),
			'updated_at' => Yii::t('Movie', 'Updated at'),
			'deleted_at' => Yii::t('Movie', 'Deleted at'),
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
				'source_attribute' => ['title', 'language.iso'],
				'slug_attribute' => 'slug',
				'replacement' => '-',
				'unique' => true,
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
			'title',
			'original_title',
			'tagline',
			'overview',
			'backdrop_path',
			'poster_path',
			'release_date',
			'budget',
			'revenue',
			'runtime',
			'status',
			'adult' => function() {
				return (boolean) $this->adult;
			},
			'homepage',
			'popularity',
			'vote_average',
			'vote_count',
			'watched' => function() {
				return array_map(function($item) {
					return $item->created_at;
				}, $this->userWatches);
			},
			'last_update' => 'updated_at',
		];
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
	public function getCompanies()
	{
		return $this->hasMany(Company::className(), ['id' => 'company_id'])->viaTable('{{%movie_company}}', ['movie_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCountries()
	{
		return $this->hasMany(Country::className(), ['id' => 'country_id'])->viaTable('{{%movie_country}}', ['movie_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getGenres()
	{
		return $this->hasMany(Genre::className(), ['id' => 'genre_id'])->viaTable('{{%movie_genre}}', ['movie_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getLanguages()
	{
		return $this->hasMany(Language::className(), ['id' => 'language_id'])->viaTable('{{%movie_language}}', ['movie_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCastPersons()
	{
		return $this->hasMany(Person::className(), ['id' => 'person_id'])
			->viaTable('{{%movie_cast}}', ['movie_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCast()
	{
		return $this->hasMany(MovieCast::className(), ['movie_id' => 'id'])
			->orderBy(['{{%movie_cast}}.[[order]]' => SORT_ASC]);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCrewPersons()
	{
		return $this->hasMany(Person::className(), ['id' => 'person_id'])
			->viaTable('{{%movie_crew}}', ['movie_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCrew()
	{
		return $this->hasMany(MovieCrew::className(), ['movie_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getSimilarMovies()
	{
		return $this->hasMany(Movie::className(), ['id' => 'similar_to_movie_id'])->viaTable('{{%movie_similar}}', ['movie_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUsers()
	{
		return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('{{%user_movie}}', ['movie_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUserWatches($id = null)
	{
		return $this->hasMany(UserMovie::className(), ['movie_id' => 'id'])
			->where(['user_id' => ($id === null) ? Yii::$app->user->id : $id]);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUserWatched()
	{
		$movie = $this;

		return $this->hasMany(User::className(), ['id' => 'user_id'])
			->viaTable('{{%user_movie}}', ['movie_id' => 'id'], function($query) use($movie) {
				$query->where([
					'user_id' => Yii::$app->user->id,
					'movie_id' => $movie->id,
				]);
			});
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPopularMovies()
	{
		return $this->hasMany(Movie::className(), ['id' => 'movie_id'])->viaTable('{{%movie_popular}}', ['movie_id' => 'id']);
	}

	protected static function getStandardRecommendations()
	{
		return Movie::find()
			->distinct()
			->select('{{%movie}}.*')
			->from([
				'{{%movie}}',
				'{{%user_movie}}',
				'{{%movie_similar}}',
				'{{%language}}',
			])
			->where(['{{%user_movie}}.[[user_id]]' => Yii::$app->user->id])
			->andWhere('{{%movie}}.[[id]] NOT IN (
				SELECT {{user_movie}}.[[movie_id]]
				FROM {{%user_movie}} AS {{user_movie}}
				WHERE {{user_movie}}.[[user_id]] = :user_id
			)')
			->andWhere('{{%movie}}.[[id]] NOT IN (
				SELECT {{user_movie_watchlist}}.[[movie_id]]
				FROM {{%user_movie_watchlist}} AS {{user_movie_watchlist}}
				WHERE {{user_movie_watchlist}}.[[user_id]] = :user_id
			)')
			->andWhere('{{%movie_similar}}.[[movie_id]] = {{%user_movie}}.[[movie_id]]')
			->andWhere('{{%movie}}.[[id]] = {{%movie_similar}}.[[similar_to_movie_id]]')
			->andWhere('{{%movie}}.[[release_date]] <= NOW()')
			->andWhere('{{%movie}}.[[language_id]] = {{%language}}.[[id]]')
			->andWhere('{{%language}}.[[iso]] = :language')
			->orderBy(['{{%movie}}.[[popularity]]' => SORT_DESC])
			->params([
				':user_id' => Yii::$app->user->id,
				':language' => Yii::$app->language,
			]);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public static function getRecommend()
	{
		try {
			$client = PredictionIOClient::factory([
				'appkey' => Yii::$app->params['prediction']['key'],
			]);
			$client->identify(Yii::$app->user->id);

			$movieIds = $client->execute($client->getCommand('itemrec_get_top_n', [
				'pio_engine' => 'movie-recommendations',
				'pio_n' => 50,
				'pio_itypes' => 'movie',
			]));
			$movieIds = array_map(function($movieId) {
				return str_replace('movie-', '', $movieId);
			}, $movieIds)['pio_iids'];

			$query = Movie::find()
				->distinct()
				->select('{{%movie}}.*')
				->from([
					'{{%movie}}',
					'{{%language}}',
				])
				->where(['in', '{{%movie}}.[[themoviedb_id]]', $movieIds])
				->andWhere('{{%movie}}.[[language_id]] = {{%language}}.[[id]]')
				->andWhere('{{%language}}.[[iso]] = :language')
				->params([
					':language' => Yii::$app->language,
				]);

			if ($query->count() <= 5) {
				return self::getStandardRecommendations();
			}

			return $query;
		} catch (Exception $e) {
			Yii::error('Error while getting user movie predictions:' . $e->getMessage());

			return self::getStandardRecommendations();
		}
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public static function getWatchlist()
	{
		return Movie::find()
			->distinct()
			->select('{{%movie}}.*')
			->from([
				'{{%movie}}',
				'{{%user_movie_watchlist}}',
			])
			->where(['{{%user_movie_watchlist}}.[[user_id]]' => Yii::$app->user->id])
			->andWhere('{{%movie}}.[[id]] = {{%user_movie_watchlist}}.[[movie_id]]')
			->orderBy(['{{%movie}}.[[release_date]]' => SORT_ASC]);
	}

	public function getOnWatchlist()
	{
		return UserMovieWatchlist::find()
			->where([
				'movie_id' => $this->id,
				'user_id' => Yii::$app->user->id,
			])
			->exists();
	}

	public function getPosterUrlSmall()
	{
		if (!empty($this->poster_path))
			return 'src="' . Yii::$app->params['themoviedb']['image_url'] . 'w92/' . $this->poster_path . '"';
		else
			return 'data-src="holder.js/92x138/#eee:#555/text:' . $this->title . '"';
	}

	public function getPosterUrl()
	{
		if (!empty($this->poster_path))
			return 'src="' . Yii::$app->params['themoviedb']['image_url'] . 'w342/' . $this->poster_path . '"';
		else
			return 'data-src="holder.js/342x513/#eee:#555/text:' . $this->title . '"';
	}

	public function getPosterUrlLarge()
	{
		if (!empty($this->poster_path))
			return 'src="' . Yii::$app->params['themoviedb']['image_url'] . 'w500/' . $this->poster_path . '"';
		else
			return 'data-src="holder.js/500x750/#eee:#555/text:' . $this->title . '"';
	}
}
