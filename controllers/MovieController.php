<?php namespace app\controllers;

use \Yii;
use \yii\filters\AccessControl;
use \yii\web\Controller;
use \yii\data\Pagination;

use \app\models\Movie;
use \app\models\Language;
use \app\models\UserMovie;
use \app\models\MoviePopular;
use \app\components\MovieDb;

class MovieController extends Controller
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['watch', 'unwatch'],
				'rules' => [
					[
						'actions' => ['watch', 'unwatch'],
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
					COUNT(DISTINCT {{%movie}}.[[id]]) AS [[row_count]]
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

			$movies = Movie::findBySql('
				SELECT DISTINCT
					{{%movie}}.*
				FROM
					{{%movie}},
					{{%user_movie}}
				WHERE
					{{%user_movie}}.[[user_id]] = :user_id AND
					{{%movie}}.[[id]] = {{%user_movie}}.[[movie_id]]
				ORDER BY
					{{%user_movie}}.[[created_at]] DESC
				LIMIT
					:offset, :limit
			', [
				':user_id' => Yii::$app->user->id,
				':offset' => $pages->offset,
				':limit' => $pages->limit,
			])->all();

			return $this->render('dashboard', [
				'movies' => $movies,
				'pages' => $pages,
			]);
		}
	}

	public function actionView($slug)
	{
		$movie = Movie::find()
			->where(['slug' => $slug])
			->with('crew')
			->with('cast')
			->with('similarMovies')
			->one();
		if ($movie === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Movie', 'The movie could not be found!'));

		$userMovies = UserMovie::find()
			->where(['user_id' => Yii::$app->user->id])
			->andWhere(['movie_id' => $movie->id])
			->all();

		return $this->render('view', [
			'movie' => $movie,
			'userMovies' => $userMovies,
		]);
	}

	public function actionLoad()
	{
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
			return json_encode([
				'success' => true,
				'slug' => $movie->slug,
				'url' => Yii::$app->urlManager->createAbsoluteUrl(['/movie/view', 'slug' => $movie->slug])
			]);

		$movie = new Movie;
		$movie->themoviedb_id = Yii::$app->request->post('id');
		$movie->language_id = $language->id;
		if (!$movie->save())
			return json_encode([
				'success' => false,
				'message' => Yii::t('Movie', 'Could not load movie! Please try again later.'),
			]);

		$movieDb = new MovieDb;

		$movie->slug = ''; // Rewrite slug with title
		if ($movieDb->syncMovie($movie)) {
			return json_encode([
				'success' => true,
				'slug' => $movie->slug,
				'url' => Yii::$app->urlManager->createAbsoluteUrl(['/movie/view', 'slug' => $movie->slug])
			]);
		} else {
			return json_encode([
				'success' => false,
				'message' => Yii::t('Movie', 'The movie could not be loaded at the moment! Please try again later.'),
			]);
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

		if (Yii::$app->request->isAjax)
			return json_encode([
				'success' => true,
			]);
		else
			return $this->redirect(['view', 'slug' => $movie->slug]);
	}

	public function actionUnwatch($id)
	{
		$userMovie = UserMovie::find()
			->where(['id' => $id])
			->andWhere(['user_id' => Yii::$app->user->id])
			->with('movie')
			->one();
		if ($userMovie === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Movie', 'You\'ve never seen the movie before!'));

		$movie = $userMovie->movie;
		$userMovie->delete();

		return $this->redirect(['view', 'slug' => $movie->slug]);
	}
}
