<?php namespace app\controllers;

use \Yii;
use \yii\filters\AccessControl;
use \yii\web\Controller;
use \yii\web\Response;

use \app\models\Movie;
use \app\models\Language;
use \app\models\UserMovie;
use \app\models\UserMovieWatchlist;
use \app\models\UserMovieRating;
use \app\components\MovieDb;
use \app\components\YiiMixpanel;

class MovieController extends Controller
{
	public function beforeAction($action)
	{
		if (Yii::$app->request->isAjax)
			$this->enableCsrfValidation = false;

		return parent::beforeAction($action);
	}

	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['watch', 'unwatch', 'rate'],
				'rules' => [
					[
						'actions' => ['watch', 'unwatch', 'rate'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
	}

	public function actionIndex()
	{
		if (Yii::$app->user->isGuest) {
			return $this->actionPopular();
		} else {
			return $this->actionDashboard();
		}
	}

	public function actionPopular()
	{
		$language = Language::find()
			->where(['iso' => Yii::$app->language])
			->orWhere(['iso' => Yii::$app->params['lang']['default_iso']])
			->one();

		$movies = Movie::findBySql('
			SELECT DISTINCT
				{{%movie}}.*
			FROM
				{{%movie}},
				{{%movie_popular}}
			WHERE
				{{%movie}}.[[language_id]] = :language_id AND
				{{%movie}}.[[id]] = {{%movie_popular}}.[[movie_id]] AND
				{{%movie}}.[[title]] != ""
			ORDER BY
				{{%movie_popular}}.[[order]] ASC
		', [
			':language_id' => $language->id,
		])
			->with('userWatches')
			->all();

		YiiMixpanel::track('Show Popular Movies');

		return $this->render('popular', [
			'movies' => $movies,
		]);
	}

	public function actionDashboard()
	{
		$movies = Movie::find()
			->select('{{%movie}}.*')
			->from([
				'{{%movie}}',
				'{{%user_movie}}',
			])
			->where(['{{%user_movie}}.[[user_id]]' => Yii::$app->user->id])
			->andWhere('{{%movie}}.[[id]] = {{%user_movie}}.[[movie_id]]')
			->orderBy(['{{%user_movie}}.[[created_at]]' => SORT_DESC])
			->limit(20)
			->all();

		$recommendDependency = new \yii\caching\TagDependency([
			'tags' => [
				'user-movie-seen-' . Yii::$app->user->id,
				'user-movie-watchlist-' . Yii::$app->user->id,
			]
		]);
		$watchlistDependency = new \yii\caching\TagDependency([
			'tags' => [
				'user-movie-watchlist-' . Yii::$app->user->id,
			]
		]);

		YiiMixpanel::track('Show Movie Dashboard');

		return $this->render('dashboard', [
			'movies' => $movies,
			'recommendMovies' => Yii::$app->db->cache(function($db) {
				return Movie::getRecommend()->limit(20)->all();
			}, 0, $recommendDependency),
			'watchlistMovies' => Yii::$app->db->cache(function($db) {
				return Movie::getWatchlist()->all();
			}, 0, $watchlistDependency),
		]);
	}

	public function actionView($slug)
	{
		$movie = Movie::find()
			->where(['slug' => $slug])
			->with([
				'language',
				'crew',
				'crew.person',
				'cast',
				'cast.person',
				'similarMovies',
				'similarMovies.userWatches',
				'genres',
			])
			->one();
		if ($movie === null) {
			$searchSlug = implode('-', array_filter(explode('-', $slug), function($item) {
				return !is_numeric($item);
			}));

			if (!empty($searchSlug)) {
				$movie = Movie::find()
					->where(['like', 'slug', $searchSlug])
					->with([
						'language',
						'crew',
						'crew.person',
						'cast',
						'cast.person',
						'similarMovies',
						'similarMovies.userWatches',
						'genres',
					])
					->one();

				if ($movie !== null)
					return $this->redirect(['view', 'slug' => $movie->slug], 301);
			}
		}

		if ($movie === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Movie', 'The movie could not be found!'));

		$userMovies = UserMovie::find()
			->where(['user_id' => Yii::$app->user->id])
			->andWhere(['movie_id' => $movie->id])
			->all();

		if ($movie->language->iso != Yii::$app->language) {
			$movieNative = Movie::find()
				->select('{{%movie}}.*')
				->distinct()
				->from([
					'{{%movie}}',
					'{{%language}}',
				])
				->where([
					'themoviedb_id' => $movie->themoviedb_id,
				])
				->andWhere('{{%movie}}.[[language_id]] = {{%language}}.[[id]]')
				->andWhere('{{%language}}.[[iso]] = :language')
				->with([
					'language',
					'crew',
					'crew.person',
					'cast',
					'cast.person',
					'similarMovies',
					'similarMovies.userWatches',
					'genres',
				])
				->params([
					':language' => Yii::$app->language,
				])
				->one();
		} else {
			$movieNative = null;
		}

		if (!Yii::$app->user->isGuest)
			$userRating = UserMovieRating::find()
				->where(['user_id' => Yii::$app->user->id])
				->where(['themoviedb_id' => $movie->themoviedb_id])
				->one();
		else
			$userRating = null;

		YiiMixpanel::track('Show Movie', [
			'language' => $movie->language->name,
		]);

		return $this->render('view', [
			'movie' => $movie,
			'userMovies' => $userMovies,
			'userRating' => $userRating,
			'movieNative' => $movieNative,
		]);
	}

	public function actionLoad()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		if (!Yii::$app->request->isPost || Yii::$app->request->post('id') === null)
			throw new yii\web\BadRequestHttpException;

		$language = Language::find()
			->where(['iso' => Yii::$app->language])
			->one();

		if ($language === null)
			$language = Language::find()
				->where(['iso' => Yii::$app->params['lang']['default_iso']])
				->one();

		$movie = Movie::find()
			->where(['themoviedb_id' => Yii::$app->request->post('id')])
			->andWhere(['language_id' => $language->id])
			->one();

		if ($movie !== null)
			return [
				'success' => true,
				'slug' => $movie->slug,
				'url' => Yii::$app->urlManager->createAbsoluteUrl(['/movie/view', 'slug' => $movie->slug])
			];

		$movie = new Movie;
		$movie->themoviedb_id = Yii::$app->request->post('id');
		$movie->language_id = $language->id;
		if (!$movie->save())
			return [
				'success' => false,
				'message' => Yii::t('Movie', 'Could not load movie! Please try again later.'),
			];

		$movieDb = new MovieDb;

		$movie->slug = ''; // Rewrite slug with title
		if ($movieDb->syncMovie($movie)) {
			YiiMixpanel::track('Load Movie', [
				'language' => $movie->language->name,
			]);

			return [
				'success' => true,
				'slug' => $movie->slug,
				'url' => Yii::$app->urlManager->createAbsoluteUrl(['/movie/view', 'slug' => $movie->slug])
			];
		} else {
			return [
				'success' => false,
				'message' => Yii::t('Movie', 'The movie could not be loaded at the moment! Please try again later.'),
			];
		}
	}

	public function actionWatch($slug)
	{
		$movie = Movie::find()
			->where(['slug' => $slug])
			->with(['language'])
			->one();
		if ($movie === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Movie', 'The movie could not be found!'));

		$userMovie = new UserMovie;
		$userMovie->user_id = Yii::$app->user->id;
		$userMovie->movie_id = $movie->id;
		$userMovie->save();

		$watchlist = UserMovieWatchlist::find()
			->where([
				'user_id' => Yii::$app->user->id,
				'movie_id' => $movie->id,
			])
			->one();

		if ($watchlist !== null)
			$watchlist->delete();

		YiiMixpanel::track('Movie Watch', [
			'language' => $movie->language->name,
		]);

		if (Yii::$app->request->isAjax) {
			Yii::$app->response->format = Response::FORMAT_JSON;

			return [
				'success' => true,
			];
		} else {
			$watchedCount = UserMovie::find()
				->where([
					'movie_id' => $movie->id,
					'user_id' => Yii::$app->user->id,
				])
				->count();

			if ($watchedCount == 1)
				Yii::$app->session->setFlash('success', Yii::t('Movie', 'You have labeled the movie as watched. You can always click on the <em>watched again</em> button to track the movie multiple times.'));
			else
				Yii::$app->session->setFlash('success', Yii::t('Movie', 'You have labeled the movie as watched again.'));

			return $this->redirect(['view', 'slug' => $movie->slug]);
		}
	}

	public function actionUnwatch($id)
	{
		$userMovie = UserMovie::find()
			->where([
				'id' => $id,
				'user_id' => Yii::$app->user->id,
			])
			->with('movie')
			->one();
		if ($userMovie === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Movie', 'You\'ve never seen the movie before!'));

		$movie = $userMovie->movie;
		$userMovie->delete();

		YiiMixpanel::track('Movie Unwatch', [
			'language' => $movie->language->name,
		]);

		return $this->redirect(['view', 'slug' => $movie->slug]);
	}

	/**
	 * Rate a movie.
	 *
	 * @param string $slug
	 * @param int $rating
	 */
	public function actionRate($slug, $rating)
	{
		if ($rating < 1 || $rating > 10)
			throw new \yii\web\BadRequestHttpException(Yii::t('Movie/Rating', 'The rating has to be between 1 and 10'));

		$movie = Movie::find()
			->where(['slug' => $slug])
			->one();

		if ($movie === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Movie/Rating', 'The movie could not be found!'));

		$movieRating = UserMovieRating::find()
			->where(['user_id' => Yii::$app->user->id])
			->where(['themoviedb_id' => $movie->themoviedb_id])
			->one();

		if ($movieRating === null) {
			$movieRating = new UserMovieRating;
			$movieRating->user_id = Yii::$app->user->id;
			$movieRating->themoviedb_id = $movie->themoviedb_id;
		}

		$movieRating->rating = $rating;

		if (Yii::$app->user->identity->hasTheMovieDBAccount()) {
			$themoviedb = new MovieDb;
			if (!$themoviedb->rateMovie(Yii::$app->user->identity, $movieRating->themoviedb_id, $movieRating->rating)) {
				$movieRating->sync = false;
				Yii::$app->session->setFlash('warning', Yii::t('Movie/Rating', 'Your rating could not be synced with themoviedb'));
			}
		}

		$movieRating->sync = true;
		$movieRating->save();

		YiiMixpanel::track('Movie Rate', [
			'language' => $movie->language->name,
			'rating' => $movieRating->rating,
		]);

		Yii::$app->session->setFlash('success', Yii::t('Movie/Rating', 'You successfully rated the movie with {count} stars.', ['count' => $movieRating->rating]));
		return $this->redirect(['view', 'slug' => $movie->slug]);
	}
}
