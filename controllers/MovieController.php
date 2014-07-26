<?php namespace app\controllers;

use \Yii;
use \yii\filters\AccessControl;
use \yii\web\Controller;
use \yii\data\Pagination;
use \yii\web\Response;

use \app\models\Movie;
use \app\models\Language;
use \app\models\UserMovie;
use \app\models\MoviePopular;
use \app\models\UserMovieWatchlist;
use \app\components\MovieDb;

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
				'only' => ['watch', 'unwatch', 'recommend'],
				'rules' => [
					[
						'actions' => ['watch', 'unwatch', 'recommend'],
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
			$language = Language::find()
				->where(['iso' => Yii::$app->language])
				->orWhere(['iso' => Yii::$app->params['lang']['default']])
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
				->all();

			return $this->render('index', [
				'movies' => $movies,
			]);
		} else {
			$countQuery = Yii::$app->db->createCommand('
				SELECT
					COUNT({{%movie}}.[[id]]) AS [[row_count]]
				FROM
					{{%movie}},
					{{%user_movie}}
				WHERE
					{{%user_movie}}.[[user_id]] = :user_id AND
					{{%movie}}.[[id]] = {{%user_movie}}.[[movie_id]]
			');
			$countQuery->bindValue(':user_id', Yii::$app->user->id);
			$countQuery = $countQuery->queryOne();

			$pages = new Pagination([
				'totalCount' => $countQuery['row_count'],
			]);

			$movies = Movie::find()
				->select('{{%movie}}.*')
				->from([
					'{{%movie}}',
					'{{%user_movie}}',
				])
				->where(['{{%user_movie}}.[[user_id]]' => Yii::$app->user->id])
				->andWhere('{{%movie}}.[[id]] = {{%user_movie}}.[[movie_id]]')
				->orderBy(['{{%user_movie}}.[[created_at]]' => SORT_DESC])
				->offset($pages->offset)
				->limit($pages->limit)
				->all();

			return $this->render('dashboard', [
				'movies' => $movies,
				'pages' => $pages,
				'recommendMovies' => Movie::getRecommend()->limit(20)->all(),
				'watchlistMovies' => Movie::getWatchlist()->all(),
			]);
		}
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
				])
				->params([
					':language' => Yii::$app->language,
				])
				->one();
		} else {
			$movieNative = null;
		}

		return $this->render('view', [
			'movie' => $movie,
			'userMovies' => $userMovies,
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
				->where(['iso' => Yii::$app->params['lang']['default']])
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

		Yii::$app->session->setFlash('event', serialize([
			'category' => 'movie',
			'action' => 'watched',
			'name' => 'add',
			'value' => $movie->id,
		]));

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
				Yii::$app->session->setFlash('success', Yii::t('Movie', 'You have marked the movie as watched. You can always click on the <em>watched again</em> button to track the movie multiple times.'));
			else
				Yii::$app->session->setFlash('success', Yii::t('Movie', 'You have marked the movie as watched again.'));

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

		Yii::$app->session->setFlash('event', serialize([
			'category' => 'movie',
			'action' => 'watched',
			'name' => 'remove',
			'value' => $movie->id,
		]));

		return $this->redirect(['view', 'slug' => $movie->slug]);
	}
}
