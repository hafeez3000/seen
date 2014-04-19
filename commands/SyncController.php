<?php namespace app\commands;

use \Yii;
use \yii\console\Controller;

use \app\components\MovieDb;
use \app\models\Show;
use \app\models\Person;
use \app\models\ShowRuntime;
use \app\models\Genre;
use \app\models\Network;
use \app\models\Country;
use \app\models\Season;
use \app\models\ShowCreator;
use \app\models\ShowGenre;
use \app\models\ShowNetwork;
use \app\models\ShowCountry;
use \app\models\Episode;
use \app\models\ShowCast;
use \app\models\ShowCrew;

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

		$shows = Show::find();

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

			$attributes = $movieDb->getShow($show);

			if ($attributes == false)
				continue;

			$show->attributes = (array) $attributes;

			if (is_array($attributes->created_by)) {
				foreach ($attributes->created_by as $creatorAttributes) {
					$person = Person::findOne($creatorAttributes->id);

					if ($person === null) {
						$person = new Person;
						$person->id = $creatorAttributes->id;
						$person->attributes = (array) $creatorAttributes;
						$person->save();

						$show->link('creators', $person);
						continue;
					}

					if (!ShowCreator::find()->where(['person_id' => $person->id, 'show_id' => $show->id])->exists())
						$show->link('creators', $person);
				}
			}

			if (is_array($attributes->episode_run_time)) {
				foreach ($attributes->episode_run_time as $minutes) {
					$runtime = ShowRuntime::findOne([
						'show_id' => $show->id,
						'minutes' => $minutes,
					]);

					if ($runtime === null) {
						$runtime = new ShowRuntime;
						$runtime->minutes = $minutes;
						$runtime->save();

						$show->link('runtimes', $runtime);
					}
				}
			}

			if (is_array($attributes->genres)) {
				foreach ($attributes->genres as $genreAttributes) {
					$genre = Genre::findOne($genreAttributes->id);

					if ($genre === null) {
						$genre = new Genre;
						$genre->id = $genreAttributes->id;
						$genre->attributes = (array) $genreAttributes;
						$genre->save();

						$show->link('genres', $genre);
						continue;
					}

					if (!ShowGenre::find()->where(['genre_id' => $genre->id, 'show_id' => $show->id])->exists())
						$show->link('genres', $genre);
				}
			}

			if (is_array($attributes->networks)) {
				foreach ($attributes->networks as $networkAttributes) {
					$network = Network::findOne($networkAttributes->id);

					if ($network === null) {
						$network = new Network;
						$network->id = $networkAttributes->id;
						$network->attributes = (array) $networkAttributes;
						$network->save();

						$show->link('networks', $network);
						continue;
					}

					if (!ShowNetwork::find()->where(['network_id' => $network->id, 'show_id' => $show->id])->exists())
						$show->link('networks', $network);
				}
			}

			if (is_array($attributes->origin_country)) {
				foreach ($attributes->origin_country as $countryName) {
					$country = Country::findOne([
						'name' => $countryName,
					]);

					if ($country === null) {
						$country = new Country;
						$country->name = $countryName;
						$country->save();

						$show->link('countries', $country);
						continue;
					}

					if (!ShowCountry::find()->where(['country_id' => $country->id, 'show_id' => $show->id])->exists())
						$show->link('countries', $country);
				}
			}

			if (is_array($attributes->seasons)) {
				foreach ($attributes->seasons as $seasonAttributes) {
					$season = Season::findOne([
						'show_id' => $show->id,
						'number' => $seasonAttributes->season_number,
					]);

					if ($season === null) {
						$season = new Season;
						$season->attributes = (array) $seasonAttributes;
						$season->number = $seasonAttributes->season_number;
						$season->save();

						$show->link('seasons', $season);
						continue;
					}

					if (!Season::find()->where(['number' => $season->number, 'show_id' => $show->id])->exists())
						$show->link('seasons', $season);
				}
			}

			if (isset($attributes->credits->cast) && is_array($attributes->credits->cast)) {
				foreach ($attributes->credits->cast as $castAttributes) {
					$cast = ShowCast::findOne($castAttributes->id);

					if ($cast === null) {
						$cast = new ShowCast;
						$cast->attributes = (array) $castAttributes;
						$cast->save();

						$show->link('cast', $cast);
						continue;
					}

					if (!ShowCast::find()->where(['id' => $cast->id])->exists())
						$show->link('cast', $cast);
				}
			}

			if (isset($attributes->credits->crew) && is_array($attributes->credits->crew)) {
				foreach ($attributes->credits->crew as $crewAttributes) {
					$crew = ShowCrew::findOne($crewAttributes->id);

					if ($crew === null) {
						$crew = new ShowCrew;
						$crew->attributes = (array) $crewAttributes;
						$crew->save();

						$show->link('crew', $crew);
						continue;
					}

					if (!ShowCrew::find()->where(['id' => $crew->id])->exists())
						$show->link('crew', $crew);
				}
			}

			if (!$show->save()) {
				Yii::warning('Could update tv show {$show->id} "' . $show->errors . '": ' . serialize($attributes), 'application\sync');
				continue;
			}
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

		foreach ($seasons->each() as $season) {
			$attributes = $movieDb->getSeason($season);

			if ($attributes == false)
				continue;

			if (is_array($attributes->episodes)) {
				foreach ($attributes->episodes as $episodeAttributes) {
					$episode = Episode::findOne([
						'season_id' => $season->id,
						'number' => $episodeAttributes->episode_number,
					]);

					if ($episode === null) {
						$episode = new Episode;
						$episode->attributes = (array) $episodeAttributes;
						$episode->number = $episodeAttributes->episode_number;
						$episode->save();

						$season->link('episodes', $episode);
						continue;
					}

					if (!Episode::find()->where(['number' => $episode->number, 'season_id' => $season->id])->exists())
						$season->link('episodes', $episode);
				}
			}

			$season->attributes = (array) $attributes;
			$season->themoviedb_id = $attributes->id;

			if (!$season->save()) {
				Yii::warning('Could update tv show season {$season->id} "' . $season->errors . '": ' . serialize($attributes), 'application\sync');
				continue;
			}
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

		foreach ($episodes->each() as $episode) {
			$attributes = $movieDb->getEpisode($episode);

			if ($attributes == false)
				continue;

			$episode->attributes = (array) $attributes;
			$episode->themoviedb_id = $attributes->id;

			if (!$episode->save()) {
				Yii::warning('Could update tv show episode {$episode->id} "' . $episode->errors . '": ' . serialize($attributes), 'application\sync');
				continue;
			}
		}

		return 0;
	}
}