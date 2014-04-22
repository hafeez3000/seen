<?php namespace app\commands;

use \Yii;
use \yii\console\Controller;

use \app\components\MovieDb;
use \app\models\Show;
use \app\models\Episode;
use \app\models\Season;
use \app\models\Movie;
use \app\models\MovieSimilar;

/**
 * Sync data with TheMovieDB.
 */
class SyncController extends Controller
{
	public $force = false;

	public $debug = false;

	public function options($actionId)
	{
		return [
			'force',
			'debug',
		];
	}

	public function actionShows()
	{
		$movieDb = new MovieDb;

		$shows = Show::find()
			->with('language');

		if (!$this->force)
			$shows = $shows->where(['updated_at' => null]);

		if ($this->debug) {
			$showCount = $shows->count();
			$i = 1;
		}

		foreach ($shows->each() as $show) {
			if ($this->debug) {
				echo "Show {$i}/{$showCount}\n";
				$i++;
			}

			$movieDb->syncShow($show);
		}

		return 0;
	}

	public function actionSeasons()
	{
		$movieDb = new MovieDb;

		$seasons = Season::find()
			->with('show');

		if (!$this->force)
			$seasons = $seasons->where(['updated_at' => null]);

		if ($this->debug) {
			$seasonCount = $seasons->count();
			$i = 1;
		}

		foreach ($seasons->each() as $season) {
			if ($this->debug) {
				echo "Season {$i}/{$seasonCount}\n";
				$i++;
			}

			$movieDb->syncSeason($season);
		}

		return 0;
	}

	public function actionEpisodes()
	{
		$movieDb = new MovieDb;

		$episodes = Episode::find()
			->with('season')
			->with('season.show');

		if (!$this->force)
			$episodes = $episodes->where(['updated_at' => null]);

		if ($this->debug) {
			$episodeCount = $episodes->count();
			$i = 1;
		}

		foreach ($episodes->each() as $episode) {
			if ($this->debug) {
				echo "Episode {$i}/{$episodeCount}\n";
				$i++;
			}

			$movieDb->syncEpisode($episode);
		}

		return 0;
	}

	public function actionMovies()
	{
		$movieDb = new MovieDb;

		// Sync similar movies
		$similarMovies = MovieSimilar::find()
			->where(['similar_to_movie_id' => null])
			->all();

		if ($this->debug) {
			$movieCount = count($similarMovies);
			$i = 1;
		}

		// Save similar movies as own movie
		foreach ($similarMovies as $similarMovie) {
			if ($this->debug) {
				echo "Similar Movie {$i}/{$movieCount}\n";
				$i++;
			}

			$newMovie = $movieDb->syncMovie($similarMovie);

			if ($newMovie !== false) {
				$similarMovie->similar_to_movie_id = $newMovie->id;
				$similarMovie->save();
			}
		}

		$movies = Movie::find()
			->with('language');

		if (!$this->force)
			$movies = $movies->where(['updated_at' => null]);

		if ($this->debug) {
			$movieCount = $movies->count();
			$i = 1;
		}

		foreach ($movies->each() as $movie) {
			if ($this->debug) {
				echo "Movie {$i}/{$movieCount}\n";
				$i++;
			}

			$movieDb->syncMovie($movie);
		}

		return 0;
	}
}