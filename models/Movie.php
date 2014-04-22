<?php namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;

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
 * @property MovieCast[] $cast
 * @property MovieCrew[] $crew
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
	public function getCast()
	{
		return $this->hasMany(MovieCast::className(), ['movie_id' => 'id'])
			->orderBy(['{{%movie_cast}}.[[order]]' => SORT_ASC]);
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

	public function getPosterUrlSmall()
	{
		if (!empty($this->poster_path))
			return Yii::$app->params['themoviedb']['image_url'] . 'w92/' . $this->poster_path;
		else
			return 'http://placehold.it/92x138/fff/555&' . http_build_query(['text' => $this->title]);
	}

	public function getPosterUrlLarge()
	{
		if (!empty($this->poster_path))
			return Yii::$app->params['themoviedb']['image_url'] . 'w500/' . $this->poster_path;
		else
			return 'http://placehold.it/300x169/fff/555&' . http_build_query(['text' => $this->title]);
	}
}
