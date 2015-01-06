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
use \app\models\Person;
use \app\models\SyncStatus;

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

	public function actionShows($themoviedb_id = null)
	{
		Yii::info('Sync shows...', 'application\sync');

		$movieDb = new MovieDb;

		$shows = Show::find()
			->with('language');

		if ($themoviedb_id !== null) {
			echo "Sync show #{$themoviedb_id}\n";
			$shows = $shows
				->with('seasons')
				->with('seasons.episodes')
				->where(['themoviedb_id' => $themoviedb_id]);
		} else {
			if (!$this->force)
				$shows = $shows->andWhere(['updated_at' => null]);
			else
				$shows = $shows
					->orWhere('updated_at <= :time', [':time' => date('Y-m-d H:i:s', time() - 3600 * 24 * 7)])
					->orWhere(['updated_at' => null]);
		}

		if ($this->debug) {
			$showCount = $shows->count();
			$i = 1;

			if ($showCount == 0) {
				echo "No shows found!\n";
			}
		}

		foreach ($shows->each() as $show) {
			if ($this->debug) {
				echo "Show {$i}/{$showCount}\n";
				$i++;
			}

			$movieDb->syncShow($show);

			if ($this->force) {
				foreach ($show->seasons as $season) {
					echo "Season {$season->number}/" . count($show->seasons) . "\n";
					$movieDb->syncSeason($season);

					foreach ($season->episodes as $episode) {
						$movieDb->syncEpisode($episode);
					}
				}
			}
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
				->where('updated_at <= :time', [':time' => date('Y-m-d H:i:s', time() - 3600 * 24 * 7)])
				->orWhere(['updated_at' => null]);

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
		Yii::info('Sync episodes...', 'application\sync');

		$movieDb = new MovieDb;

		$episodes = Episode::find()
			->with('season')
			->with('season.show');

		if (!$this->force)
			$episodes = $episodes->where(['updated_at' => null]);
		else
			$episodes = $episodes
				->where('updated_at <= :time', [':time' => date('Y-m-d H:i:s', time() - 3600 * 24 * 7)])
				->orWhere(['updated_at' => null])
				->orderBy(['updated_at' => SORT_ASC]);

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

	public function actionTvChanges($id = null)
	{
		Yii::info('Sync tv changes...', 'application\sync');

		$movieDb = new MovieDb;

		if ($id === null) {
			$tvChanges = $movieDb->getTvChanges();

			if ($this->debug) {
				$changesCount = count($tvChanges);
				$i = 1;
			}

			$syncStatus = SyncStatus::find()
				->where([
					'name' => 'tv_changes',
					'updated' => date('Y-m-d'),
				])
				->one();

			if ($syncStatus !== null) {
				$completedChanges = unserialize($syncStatus->value);
			} else {
				$completedChanges = [];
				$syncStatus = new SyncStatus;
				$syncStatus->name = 'tv_changes';
				$syncStatus->updated = date('Y-m-d');
			}
		} else {
			$tvChanges = [$id];
			$changesCount = 1;
			$i = 1;
		}

		foreach ($tvChanges as $tvChange) {
			if ($this->debug) {
				echo "Update tv change {$i}/{$changesCount}\n";
				$i++;
			}

			if ($id === null && in_array($tvChange, $completedChanges))
				continue;

			$movieDb->syncTvChange($tvChange);

			if ($id === null) {
				$completedChanges[] = $tvChange;
				$syncStatus->value = serialize($completedChanges);
				$syncStatus->save();
			}
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
			if ($this->debug) {
				echo "Similar Movie {$i}/{$movieCount}\n";
				$i++;
			}

			$movieDb->syncMovie($similarMovie);
		}
	}

	public function actionMovies($id = null)
	{
		Yii::info('Sync movies...', 'application\sync');

		$movieDb = new MovieDb;

		$movies = Movie::find()
			->with('language');

		if ($id !== null) {
			echo "Sync movie {$id}...\n";
			$movies = $movies->andWhere(['id' => $id]);
		}

		if (!$this->force)
			$movies = $movies->andWhere(['updated_at' => null]);
		else
			$movies = $movies
				->where('updated_at <= :time', [':time' => date('Y-m-d H:i:s', time() - 3600 * 24)])
				->orWhere(['updated_at' => null]);

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
	}

	public function actionMoviesChanges()
	{
		Yii::info('Sync movie changes...', 'application\sync');

		$movieDb = new MovieDb;

		$movieChanges = $movieDb->getMovieChanges();

		if ($this->debug) {
			$changesCount = count($movieChanges);
			$i = 1;
		}

		$syncStatus = SyncStatus::find()
			->where([
				'name' => 'movie_changes',
				'updated' => date('Y-m-d'),
			])
			->one();

		if ($syncStatus !== null) {
			$completedChanges = unserialize($syncStatus->value);
		} else {
			$completedChanges = [];
			$syncStatus = new SyncStatus;
			$syncStatus->name = 'movie_changes';
			$syncStatus->updated = date('Y-m-d');
		}

		$movies = Movie::find()
			->where(['themoviedb_id' => $movieChanges]);

		if ($this->debug) {
			$changesCount = $movies->count();
			$i = 1;
		}

		foreach ($movies->each() as $movie) {
			if ($this->debug) {
				echo "Update movie {$i}/{$changesCount}\n";
				$i++;
			}

			if (in_array($movie->themoviedb_id, $completedChanges))
				continue;

			$movieDb->syncMovie($movie);

			$completedChanges[] = $movie->themoviedb_id;
			$syncStatus->value = serialize($completedChanges);
			$syncStatus->save();
		}

		return 0;
	}

	public function actionPopularMovies()
	{
		Yii::info('Sync popular movies...', 'application\sync');

		$movieDb = new MovieDb;

		$languages = Language::find();

		if (!$this->force)
			$languages = $languages
				->where(['popular_movies_updated_at' => null])
				->orWhere('[[popular_movies_updated_at]] <= :time')
				->addParams([
					':time' => date('Y-m-d H:i:s', time() - (3600 * 24 * 7))
				]);

		if ($this->debug) {
			$languageCount = $languages->count();
			$i = 1;
		}

		foreach ($languages->each() as $language) {
			$oldPopularMovies = MoviePopular::find()
				->leftJoin(Movie::tableName(), '{{%movie_popular}}.[[movie_id]] = {{%movie}}.[[id]]')
				->where(['{{%movie}}.[[language_id]]' => $language->id])
				->all();

			foreach ($oldPopularMovies as $movie) {
				$movie->delete();
			}

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

			$language->popular_movies_updated_at = date('Y-m-d H:i:s');
			$language->save();
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

	public function actionPersons()
	{
		Yii::info('Sync popular shows...', 'application\sync');

		$movieDb = new MovieDb;
		$personChanges = $movieDb->getPersonChanges();

		$persons = Person::find();

		$syncStatus = SyncStatus::find()
			->where([
				'name' => 'person_changes',
				'updated' => date('Y-m-d'),
			])
			->one();

		if ($syncStatus !== null) {
			$completedChanges = unserialize($syncStatus->value);
		} else {
			$completedChanges = [];
			$syncStatus = new SyncStatus;
			$syncStatus->name = 'person_changes';
			$syncStatus->updated = date('Y-m-d');
		}

		if (!$this->force)
			$persons = $persons
				->where(['id' => $personChanges])
				->orWhere(['updated_at' => null]);
		else
			$persons = $persons
				->where(['updated_at' => null])
				->orWhere('[[updated_at]] <= :time')
				->addParams([':time' => date('Y-m-d H:i:s', time() - 3600 * 24)]);

		if ($this->debug) {
			$personCount = $persons->count();
			$i = 1;
		}

		foreach ($persons->each() as $person) {
			if ($this->debug) {
				echo "Update person {$i}/{$personCount}\n";
				$i++;
			}

			if (in_array($person->id, $completedChanges))
				continue;

			$movieDb->syncPerson($person);

			$completedChanges[] = $person->id;
			$syncStatus->value = serialize($completedChanges);
			$syncStatus->save();
		}
	}
}
