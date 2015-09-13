<?php namespace app\controllers;

use \Yii;
use \yii\web\Controller;
use \yii\filters\AccessControl;

use \app\models\Movie;
use \app\models\UserMovieWatchlist;
use \app\components\YiiMixpanel;

class WatchlistController extends Controller
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['add', 'remove'],
				'rules' => [
					[
						'actions' => ['add', 'remove'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
	}

	public function actionAdd($slug)
	{
		$movie = Movie::find()->where(['slug' => $slug])->one();
		if ($movie === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('User/MovieWatchlist', 'The movie could not be found!'));

		$watchlist = UserMovieWatchlist::find()
			->where([
					'movie_id' => $movie->id,
					'user_id' => Yii::$app->user->id,
			])
			->one();

		if ($watchlist !== null) {
			Yii::$app->session->setFlash('warning', Yii::t('User/MovieWatchlist', 'The movie is already on your watchlist!'));
		} else {
			YiiMixpanel::track('Add Movie to Watchlist', [
				'movie' => $movie->themoviedb_id,
				'language' => $movie->language->name,
			]);

			$watchlist = new UserMovieWatchlist;
			$watchlist->movie_id = $movie->id;
			$watchlist->user_id = Yii::$app->user->id;
			$watchlist->save();
		}

		return $this->redirect(['movie/view', 'slug' => $movie->slug]);
	}

	public function actionRemove($slug)
	{
		$movie = Movie::find()->where(['slug' => $slug])->one();
		if ($movie === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('User/MovieWatchlist', 'The movie could not be found!'));

		$watchlist = UserMovieWatchlist::find()
			->where([
					'movie_id' => $movie->id,
					'user_id' => Yii::$app->user->id,
			])
			->one();

		if ($watchlist !== null) {
			$watchlist->delete();

			YiiMixpanel::track('Remove Movie from Watchlist', [
				'movie' => $movie->themoviedb_id,
				'language' => $movie->language->name,
			]);
		}

		return $this->redirect(['movie/view', 'slug' => $movie->slug]);
	}
}
