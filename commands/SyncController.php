<?php namespace app\commands;

use \Yii;
use \yii\console\Controller;

use \app\components\MovieDb;
use \app\models\Show;
use \app\models\Episode;
use \app\models\Season;
use \app\models\Movie;
use \app\models\MovieSimilar;
use \app\models\MoviePopular;
use \app\models\ShowPopular;
use \app\models\Language;

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
		Yii::info('Sync shows...', 'application\sync');

		$movieDb = new MovieDb;

		$shows = Show::find()
			->with('language');

		if (!$this->force)
			$shows = $shows->where(['updated_at' => null]);
		else
			$shows = $shows
				->where('updated_at <= :time', [':time' => date('Y-m-d H:i:s', time() - 3600 * 24)])
				->orWhere(['updated_at' => null]);

		if ($this->debug) {
			$showCount = $shows->count();
			$i = 1;
		}

		foreach ($shows->each() as $show) {
			Yii::getLogger()->flush();

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
		Yii::info('Sync seasons...', 'application\sync');

		$movieDb = new MovieDb;

		$seasons = Season::find()
			->with('show');

		if (!$this->force)
			$seasons = $seasons->where(['updated_at' => null]);
		else
			$seasons = $seasons
				->where('updated_at <= :time', [':time' => date('Y-m-d H:i:s', time() - 3600 * 24)])
				->orWhere(['updated_at' => null]);

		if ($this->debug) {
			$seasonCount = $seasons->count();
			$i = 1;
		}

		foreach ($seasons->each() as $season) {
			Yii::getLogger()->flush();

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
		Yii::info('Sync episodes...', 'application\sync');

		$movieDb = new MovieDb;

		$episodes = Episode::find()
			->with('season')
			->with('season.show');

		if (!$this->force)
			$episodes = $episodes->where(['updated_at' => null]);
		else
			$episodes = $episodes
				->where('updated_at <= :time', [':time' => date('Y-m-d H:i:s', time() - 3600 * 24)])
				->orWhere(['updated_at' => null]);

		if ($this->debug) {
			$episodeCount = $episodes->count();
			$i = 1;
		}

		foreach ($episodes->each() as $episode) {
			Yii::getLogger()->flush();

			if ($this->debug) {
				echo "Episode {$i}/{$episodeCount}\n";
				$i++;
			}

			$movieDb->syncEpisode($episode);
		}

		return 0;
	}

	public function actionMoviesSimilar()
	{
		Yii::info('Sync similar movies...', 'application\sync');

		$movieDb = new MovieDb;

		$similarMovies = MovieSimilar::find()
			->where(['similar_to_movie_id' => null]);

		if ($this->debug) {
			$movieCount = $similarMovies->count();
			$i = 1;
		}

		// Save similar movies as own movie
		foreach ($similarMovies->each() as $similarMovie) {
			Yii::getLogger()->flush();

			if ($this->debug) {
				echo "Similar Movie {$i}/{$movieCount}\n";
				$i++;
			}

			$movieDb->syncMovie($similarMovie);
		}
	}

	public function actionMovies()
	{
		Yii::info('Sync movies...', 'application\sync');

		$movieDb = new MovieDb;

		$movies = Movie::find()
			->with('language');

		if (!$this->force)
			$movies = $movies->where(['updated_at' => null]);

		if ($this->debug) {
			$movieCount = $movies->count();
			$i = 1;
		}

		foreach ($movies->each() as $movie) {
			Yii::getLogger()->flush();

			if ($this->debug) {
				echo "Movie {$i}/{$movieCount}\n";
				$i++;
			}

			$movieDb->syncMovie($movie);
		}
	}

	public function actionMoviesChanges()
	{
		Yii::info('Sync movie changes...', 'application\sync');

		$movieDb = new MovieDb;

		$movieChanges = $movieDb->getMovieChanges();

		$movies = Movie::find()
			->where(['themoviedb_id' => $movieChanges]);

		if ($this->debug) {
			$changesCount = $movies->count();
			$i = 1;
		}

		foreach ($movies->each() as $movie) {
			Yii::getLogger()->flush();

			if ($this->debug) {
				echo "Update movie {$i}/{$changesCount}\n";
				$i++;
			}

			$movieDb->syncMovie($movie);
		}

		return 0;
	}

	public function actionPopularMovies()
	{
		Yii::info('Sync popular movies...', 'application\sync');

		$movieDb = new MovieDb;

		$languages = Language::find();

		if ($this->debug) {
			$languageCount = $languages->count();
			$i = 1;
		}

		MoviePopular::deleteAll();

		foreach ($languages->each() as $language) {
			Yii::getLogger()->flush();

			if ($this->debug) {
				echo "Get popular movies for language {$language->iso} {$i}/{$languageCount}\n";
				$i++;
			}

			$popularMoviesAttribute = $movieDb->getPopularMovies($language->iso);
			$order = 0;

			foreach ($popularMoviesAttribute->results as $movieAttribute) {
				$order++;

				if ($this->debug) {
					echo "Save popular movie {$order}/20\n";
				}

				$movie = Movie::find()
					->where(['themoviedb_id' => $movieAttribute->id])
					->andWhere(['language_id' => $language->id])
					->one();

				if ($movie === null) {
					$movie = new Movie;
					$movie->themoviedb_id = $movieAttribute->id;
					$movie->language_id = $language->id;
					if (!$movie->save())
						Yii::error("Could not save movie: " . serialize($movie->errors) . "!", 'application\sync');

					$movie->slug = '';
					$movieDb->syncMovie($movie);
				}

				$popularMovie = new MoviePopular;
				$popularMovie->movie_id = $movie->id;
				$popularMovie->order = $order;
				if (!$popularMovie->save())
					Yii::error("Could not save popular movie: " . serialize($popularMovie->errors) . "!", 'application\sync');
			}
		}
	}

	public function actionPopularShows()
	{
		Yii::info('Sync popular shows...', 'application\sync');

		$movieDb = new MovieDb;

		$languages = Language::find();

		if (!$this->force)
			$languages = $languages
				->where(['popular_shows_updated_at' => null])
				->orWhere('[[popular_shows_updated_at]] <= :time')
				->addParams([
					':time' => date('Y-m-d H:i:s', time() - (3600 * 24 * 7))
				]);

		if ($this->debug) {
			$languageCount = $languages->count();
			$i = 1;
		}

		foreach ($languages->each() as $language) {
			Yii::getLogger()->flush();

			$oldPopularShows = ShowPopular::find()
				->leftJoin(Show::tableName(), '{{%show_popular}}.[[show_id]] = {{%show}}.[[id]]')
				->where(['{{%show}}.[[language_id]]' => $language->id])
				->all();

			foreach ($oldPopularShows as $show) {
				$show->delete();
			}

			if ($this->debug) {
				echo "Get popular tv shows for language {$language->iso} {$i}/{$languageCount}\n";
				$i++;
			}

			$popularShowsAttribute = $movieDb->getPopularShows($language->iso);
			$order = 0;

			foreach ($popularShowsAttribute->results as $showAttribute) {
				$order++;

				if ($this->debug) {
					echo "Save popular tv show {$order}/20\n";
				}

				$show = Show::find()
					->where(['themoviedb_id' => $showAttribute->id])
					->andWhere(['language_id' => $language->id])
					->one();

				if ($show === null) {
					$show = new Show;
					$show->themoviedb_id = $showAttribute->id;
					$show->language_id = $language->id;
					if (!$show->save())
						Yii::error("Could not save tv show: " . serialize($show->errors) . "!", 'application\sync');

					$show->slug = '';
					$movieDb->syncShow($show);
				}

				$this->actionSeasons();

				$popularShow = new ShowPopular;
				$popularShow->show_id = $show->id;
				$popularShow->order = $order;
				if (!$popularShow->save())
					Yii::error("Could not save popular tv show: " . serialize($popularShow->errors) . "!", 'application\sync');
			}

			$language->popular_shows_updated_at = date('Y-m-d H:i:s');
			$language->save();
		}
	}
}
