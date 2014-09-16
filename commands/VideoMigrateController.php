<?php namespace app\commands;

use \Yii;
use \yii\console\Controller;

use \app\components\MovieDb;
use \app\models\Movie;
use \app\models\MovieVideo;
use \app\models\Show;
use \app\models\ShowVideo;

class VideoMigrateController extends Controller
{
	public function actionMovies()
	{
		$movieDb = new MovieDb;
		$movieVideos = MovieVideo::find()
			->select('movie_id')
			->distinct()
			->asArray()
			->all();

		$movieQuery = Movie::find()
			->where(['not in', 'id', array_map(function($movieId) {
				return $movieId['movie_id'];
			}, $movieVideos)]);

		$movieCount = $movieQuery->count();
		$i = 1;

		foreach ($movieQuery->each() as $movie) {
			echo "Movie {$i}/{$movieCount}\n";

			$i++;
			$movieDb->syncMovie($movie);
		}
	}

	public function actionShows()
	{
		$movieDb = new MovieDb;
		$showVideos = ShowVideo::find()
			->select('show_id')
			->distinct()
			->asArray()
			->all();

		$showQuery = Show::find()
			->where(['not in', 'id', array_map(function($showId) {
				return $showId['show_id'];
			}, $showVideos)]);;

		$i = 1;
		$showCount = $showQuery->count();

		foreach ($showQuery->each() as $show) {
			echo "Movie {$i}/{$showCount}\n";

			$i++;
			$movieDb->syncShow($show);
		}
	}
}
