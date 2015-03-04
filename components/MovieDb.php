<?php namespace app\components;

use \Yii;

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
use \app\models\Movie;
use \app\models\MovieSimilar;
use \app\models\MovieCast;
use \app\models\MovieCrew;
use \app\models\MovieGenre;
use \app\models\MovieCompany;
use \app\models\MovieCountry;
use \app\models\MovieLanguage;
use \app\models\Company;
use \app\models\Language;
use \app\models\ShowVideo;
use \app\models\MovieVideo;
use \app\models\UserMovieRating;
use \app\models\UserShowRating;

class MovieDb
{
	private $key = '';

	private $lastStatus = null;

	protected $errors = [];

	public function __construct()
	{
		$this->key = Yii::$app->params['themoviedb']['key'];
	}

	private function getCurrentRate()
	{
		$rateQuery = Yii::$app->db->createCommand('SELECT COUNT([[id]]) as [[count]] FROM {{%themoviedb_rate}} WHERE [[created_at]] > :created_at');
		$rateQuery->bindValue(':created_at', date('Y-m-d H:i:s', time() - 10));
		$rate = $rateQuery->queryOne();

		return $rate['count'];
	}

	private function raiseRate()
	{
		$command = Yii::$app->db->createCommand('INSERT INTO {{%themoviedb_rate}}([[created_at]]) VALUES(:created_at)');
		$command->bindValue(':created_at', date('Y-m-d H:i:s'));

		return ($command->execute() > 0);
	}

	private function throttle()
	{
		sleep(1);
	}

	protected function get($path, $parameters = [])
	{
		$rate = $this->getCurrentRate();

		while ($rate >= 30) {
			$this->throttle();

			$rate = $this->getCurrentRate();
		}

		$this->raiseRate();

		$parameters = array_merge_recursive($parameters, [
			'api_key' => $this->key,
		]);
		$url = Yii::$app->params['themoviedb']['url'] . $path . '?' . http_build_query($parameters);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);

		Yii::trace("Execute request to {$url} with parameters...", 'application\sync');

		$this->lastStatus = null;
		$response = curl_exec($curl);
		if ($response === false) {
			Yii::error("Error while requesting {$path}");
			$this->errors[] = "Error while requesting {$path}";
			return false;
		}

		$status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ($status < 200 || $status >= 400) {
			Yii::error("Error while requesting {$url}, code {$status}: " . $response);
			$this->errors[] = "Error while requesting {$path}, code {$status}: " . $response;
			$this->lastStatus = $status;
			return false;
		}

		Yii::trace("Executed get request successfully ({$status}) to {$url} with parameters.", 'application\sync');

		$result = json_decode($response);
		curl_close($curl);

		if ($result === false)
			Yii::warning("Could not decode json response from {url}!");

		return $result;
	}

	protected function post($path, $parameters = [], $data = [])
	{
		$rate = $this->getCurrentRate();

		while ($rate >= 30) {
			$this->throttle();

			$rate = $this->getCurrentRate();
		}

		$this->raiseRate();

		$parameters = array_merge_recursive($parameters, [
			'api_key' => $this->key,
		]);
		$url = Yii::$app->params['themoviedb']['url'] . $path . '?' . http_build_query($parameters);
		$dataString = json_encode($data);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $dataString);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($dataString))
		);

		Yii::trace("Execute post request to {$url} with parameters...", 'application\sync');

		$this->lastStatus = null;
		$response = curl_exec($curl);
		if ($response === false) {
			Yii::error("Error while requesting {$path}");
			$this->errors[] = "Error while requesting {$path}";
			return false;
		}

		$status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ($status < 200 || $status >= 400) {
			Yii::error("Error while requesting {$url}, code {$status}: " . $response);
			$this->errors[] = "Error while requesting {$path}, code {$status}: " . $response;
			$this->lastStatus = $status;
			return false;
		}

		Yii::trace("Executed request successfully ({$status}) to {$url} with parameters.", 'application\sync');

		$result = json_decode($response);
		curl_close($curl);

		if ($result === false)
			Yii::warning("Could not decode json response from {url}!");

		return $result;
	}

	protected function paginate($path, $parameters = [])
	{
		$page = 1;
		$results = $this->get($path, array_merge($parameters, ['page' => $page]));
		$output = [];

		if (isset($results->results)) {
			$output = array_merge($results->results, $output);

			if (!isset($results->total_pages)) {
				return $output;
			}

			while ($results->total_pages > $page) {
				$page++;
				$results = $this->get($path, array_merge($parameters, ['page' => $page]));

				if (isset($results->results))
					$output = array_merge($results->results, $output);
			}
		}

		return $output;
	}

	public function lastError()
	{
		return end($this->errors);
	}

	public function findTvDb($id, $language, $name, $imdbid = '', $year = '')
	{
		// Search by tvdb id
		$results = $this->get(sprintf('/find/%s', $id), [
			'external_source' => 'tvdb_id',
			'language' => $language,
		]);

		if (isset($results->tv_results) && count($results->tv_results)) {
			echo "Found by TheTVDb";
			return (array) $results->tv_results[0];
		}

		// Search by imdb id
		if (!empty($imdbid)) {
			$results = $this->get(sprintf('/find/%s', $imdbid), [
				'external_source' => 'imdb_id',
				'language' => $language,
			]);

			if (isset($results->tv_results) && count($results->tv_results)) {
				echo "Found by IMDB";
				return (array) $results->tv_results[0];
			}
		}

		// Search by name and year
		$parameters = [
			'query' => $name,
			'language' => $language,
		];
		if (!empty($year)) {
			$parameters['year'] = $year;
		}
		$results = $this->get('/search/tv', $parameters);
		if (isset($results->results) && count($results->results)) {
			echo "Found by search and year";

			return (array) $results->results;
		}

		// Search by name
		$parameters = [
			'query' => $name,
			'language' => $language,
		];
		$results = $this->get('/search/tv', $parameters);
		if (isset($results->results) && count($results->results)) {
			echo "Found by search";

			return (array) $results->results;
		}

		return false;
	}

	public function getShow($show)
	{
		return $this->get(sprintf('/tv/%s', $show->themoviedb_id), [
			'language' => $show->language->iso,
			'append_to_response' => 'credits,videos',
		]);
	}

	public function getSeason($season)
	{
		$number = isset($season->number) ? $season->number : 0;

		return $this->get(sprintf('/tv/%s/season/%s', $season->show->themoviedb_id, $number), [
			'language' => $season->show->language->iso,
		]);
	}

	public function getEpisode($episode)
	{
		$episodeNumber = !empty($episode->number) ? $episode->number : '0';

		return $this->get(sprintf('/tv/%s/season/%s/episode/%s', $episode->season->show->themoviedb_id, $episode->season->number, $episodeNumber), [
			'language' => $episode->season->show->language->iso,
		]);
	}

	public function getMovie($movie, $language = null)
	{
		if (get_class($movie) == Movie::className()) {
			$themoviedbId = $movie->themoviedb_id;
			$language = $movie->language->iso;
		} else {
			$themoviedbId = $movie->similar_to_themoviedb_id;
		}

		return $this->get(sprintf('/movie/%s', $themoviedbId), [
			'language' => $language,
			'append_to_response' => 'credits,similar_movies,videos',
		]);
	}

	public function getPerson($person)
	{
		return $this->get(sprintf('/person/%s', $person->id));
	}

	public function getPopularMovies($language)
	{
		return $this->get('/movie/popular', [
			'language' => $language,
			'page' => '1',
		]);
	}

	public function getPopularShows($language)
	{
		return $this->get('/tv/popular', [
			'language' => $language,
			'page' => '1',
		]);
	}

	public function getTvChanges($startDate = null, $endDate = null)
	{
		$results = $this->paginate('/tv/changes', [
			'start_date' => ($startDate === null) ? date('Y-m-d', (time() - 3600 * 24 * 3)) : date('Y-m-d', strtotime($startDate)),
		]);

		return array_map(function($arr) {
			return $arr->id;
		}, $results);
	}

	public function getTvChange($id, $startDate = null, $endDate = null)
	{
		return $this->get(sprintf('/tv/%s/changes', $id), [
			'start_date' => ($startDate === null) ? date('Y-m-d', (time() - 3600 * 24 * 3)) : date('Y-m-d', strtotime($startDate)),
		]);
	}

	public function getSeasonChanges($id, $startDate = null, $endDate = null)
	{
		return $this->get(sprintf('/tv/season/%s/changes', $id), [
			'start_date' => ($startDate === null) ? date('Y-m-d', (time() - 3600 * 24 * 3)) : date('Y-m-d', strtotime($startDate)),
		]);
	}

	public function getEpisodeChanges($id, $startDate = null, $endDate = null)
	{
		return $this->get(sprintf('/tv/episode/%s/changes', $id), [
			'start_date' => ($startDate === null) ? date('Y-m-d', (time() - 3600 * 24 * 3)) : date('Y-m-d', strtotime($startDate)),
		]);
	}

	public function getMovieChanges($startDate = null, $endDate = null)
	{
		$results = $this->paginate('/movie/changes', [
			'start_date' => ($startDate === null) ? date('Y-m-d', (time() - 3600 * 24)) : date('Y-m-d', strtotime($startDate)),
			'end_date' => ($endDate === null) ? date('Y-m-d') : date('Y-m-d', strtotime($endDate)),
		]);

		return array_map(function($arr) {
			return $arr->id;
		}, $results);
	}

	public function getPersonChanges($startDate = null, $endDate = null)
	{
		$results = $this->paginate('/person/changes', [
			'start_date' => ($startDate === null) ? date('Y-m-d', (time() - 3600 * 24)) : date('Y-m-d', strtotime($startDate)),
			'end_date' => ($endDate === null) ? date('Y-m-d') : date('Y-m-d', strtotime($endDate)),
		]);

		return array_map(function($arr) {
			return $arr->id;
		}, $results);
	}

	public function syncShow($show)
	{
		Yii::info("Syncing tv show #{$show->id} '{$show->name}'...", 'application\sync');

		$attributes = $this->getShow($show);

		if ($attributes === false) {
			Yii::error("Could not get attributes from api for show #{$show->id}...", 'application\sync');

			if ($this->lastStatus == 404)
				$show->delete();

			return false;
		}

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
				$person = Person::findOne($castAttributes->id);

				if ($person === null) {
					$person = new Person;
					$person->attributes = (array) $castAttributes;
					$person->save();
				}

				$cast = ShowCast::find()
					->where([
						'show_id' => $show->id,
						'person_id' => $person->id,
					])
					->one();

				if ($cast === null) {
					$cast = new ShowCast;
					$cast->attributes = (array) $castAttributes;
					$cast->id = null;
					$cast->show_id = $show->id;
					$cast->person_id = $castAttributes->id;

					if (!$cast->save()) {
						Yii::error("Could not save cast: " . serialize($cast->errors));
					}
				}
			}
		}

		if (isset($attributes->credits->crew) && is_array($attributes->credits->crew)) {
			foreach ($attributes->credits->crew as $crewAttributes) {
				$person = Person::findOne($crewAttributes->id);

				if ($person === null) {
					$person = new Person;
					$person->attributes = (array) $crewAttributes;
					$person->save();
				}

				$crew = ShowCrew::find()
					->where([
						'show_id' => $show->id,
						'person_id' => $person->id,
					])
					->one();

				if ($crew === null) {
					$crew = new ShowCrew;
					$crew->attributes = (array) $crewAttributes;
					$crew->id = null;
					$crew->show_id = $show->id;
					$crew->person_id = $crewAttributes->id;
					$crew->save();
				}
			}
		}

		if (isset($attributes->videos->results) && is_array($attributes->videos->results)) {
			foreach ($attributes->videos->results as $videoAttributes) {
				$video = ShowVideo::findOne($videoAttributes->id);

				if ($video === null) {
					$video = new ShowVideo;
					$video->id = $videoAttributes->id;
					$video->show_id = $show->id;
					$video->key = $videoAttributes->key;
					$video->name = $videoAttributes->name;
					$video->site = $videoAttributes->site;
					$video->size = $videoAttributes->size;
					$video->type = $videoAttributes->type;

					$video->save();
				}
			}
		}

		if (!$show->save()) {
			Yii::warning("Could update tv show #{$show->id} '" . serialize($show->errors) . "': " . serialize($attributes), 'application\sync');
			return false;
		}

		return true;
	}

	public function syncSeason($season)
	{
		Yii::info("Syncing tv show season #{$season->id}...", 'application\sync');

		$attributes = $this->getSeason($season);

		if ($attributes === false) {
			Yii::error("Could not get attributes from api for season #{$season->id}...", 'application\sync');

			if ($this->lastStatus == 404)
				$season->delete();

			return false;
		}

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
				} else {
					$episode->attributes = (array) $episodeAttributes;
					$episode->number = $episodeAttributes->episode_number;
					$episode->save();
				}

				if (!Episode::find()->where(['number' => $episode->number, 'season_id' => $season->id])->exists())
					$season->link('episodes', $episode);
			}
		}

		$season->attributes = (array) $attributes;
		$season->themoviedb_id = $attributes->id;

		if (!$season->save()) {
			Yii::warning("Could update tv show season #{$season->id} '" . serialize($season->errors) . "': " . serialize($attributes), 'application\sync');
			return false;
		}

		return true;
	}

	public function syncEpisode($episode)
	{
		Yii::info("Syncing episode #{$episode->id}...", 'application\sync');

		$attributes = $this->getEpisode($episode);

		if ($attributes === false) {
			Yii::error("Could not get attributes from api for episode #{$episode->id}...", 'application\sync');

			if ($this->lastStatus == 404)
				$episode->delete();

			return false;
		}

		$episode->attributes = (array) $attributes;
		$episode->themoviedb_id = $attributes->id;

		if (!$episode->save()) {
			Yii::warning("Could update tv show episode {$episode->id} '" . serialize($episode->errors) . "': " . serialize($attributes), 'application\sync');
			return false;
		}

		return true;
	}

	public function syncMovie(&$movie, $language = null)
	{
		if (get_class($movie) == Movie::className()) {
			$isSimilarMovie = false;
			Yii::info("Syncing movie #{$movie->id} '{$movie->completeTitle}'...", 'application\sync');
		} else {
			$isSimilarMovie = true;
			Yii::info("Syncing similar movie #{$movie->id}...", 'application\sync');
		}

		if ($isSimilarMovie) {
			$similarMovie = $movie;
			$language = Language::find(['iso' => $language])->one();

			$movie = Movie::find()
				->where([
					'themoviedb_id' => $similarMovie->similar_to_themoviedb_id,
					'language_id' => $language->id,
				])
				->one();

			if ($movie === null) {
				$movie = new Movie;
				$movie->themoviedb_id = $similarMovie->similar_to_themoviedb_id;
				$movie->language_id = $language->id;
				$movie->save();
			} else {
				$similarMovie->similar_to_movie_id = $movie->id;
				$similarMovie->save();

				return true;
			}
		}

		$attributes = $this->getMovie($movie, $language);

		if ($attributes === false) {
			Yii::error("Could not get attributes from api for movie #{$movie->id}...", 'application\sync');

			if ($this->lastStatus == 404)
				$movie->delete();

			return false;
		}

		$movie->attributes = (array) $attributes;

		if ($movie->isNewRecord)
			$movie->save();

		if (isset($attributes->similar_movies->results) && is_array($attributes->similar_movies->results)) {
			foreach ($attributes->similar_movies->results as $similarMovieAttributes) {
				$similarMovie = MovieSimilar::findOne([
					'movie_id' => $movie->id,
					'similar_to_themoviedb_id' => $similarMovieAttributes->id,
				]);

				if ($similarMovie === null) {
					$similarMovieModel = Movie::find()
						->where(['themoviedb_id' => $similarMovieAttributes->id])
						->andWhere(['language_id' => $movie->language_id])
						->one();

					$similarMovie = new MovieSimilar;
					$similarMovie->movie_id = $movie->id;
					$similarMovie->similar_to_movie_id = ($similarMovieModel !== null) ? $similarMovieModel->id : null;
					$similarMovie->similar_to_themoviedb_id = $similarMovieAttributes->id;
					$similarMovie->save();
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

					$movie->link('genres', $genre);
					continue;
				}

				if (!MovieGenre::find()->where(['genre_id' => $genre->id, 'movie_id' => $movie->id])->exists())
					$movie->link('genres', $genre);
			}
		}

		if (is_array($attributes->production_companies)) {
			foreach ($attributes->production_companies as $companyAttributes) {
				$company = Company::findOne($companyAttributes->id);

				if ($company === null) {
					$company = new Company;
					$company->attributes = (array) $companyAttributes;
					$company->id = $companyAttributes->id;
					$company->save();

					$movie->link('companies', $company);
					continue;
				}

				if (!MovieCompany::find()->where(['company_id' => $company->id, 'movie_id' => $movie->id])->exists())
					$movie->link('companies', $company);
			}
		}

		if (is_array($attributes->production_countries)) {
			foreach ($attributes->production_countries as $countryAttributes) {
				$country = Country::findOne([
					'name' => $countryAttributes->name,
				]);

				if ($country === null) {
					$country = new Country;
					$country->name = $countryAttributes->name;
					$country->save();

					$movie->link('countries', $country);
					continue;
				}

				if (!MovieCountry::find()->where(['country_id' => $country->id, 'movie_id' => $movie->id])->exists())
					$movie->link('countries', $country);
			}
		}

		if (is_array($attributes->spoken_languages)) {
			foreach ($attributes->spoken_languages as $languageAttributes) {
				$language = Language::findOne([
					'iso' => $languageAttributes->iso_639_1,
				]);

				if ($language === null) {
					$language = new Language;
					$language->iso = $languageAttributes->iso_639_1;
					$language->name = $languageAttributes->iso_639_1;
					$language->save();

					$movie->link('languages', $language);
					continue;
				}

				if (!MovieLanguage::find()->where(['language_id' => $language->id, 'movie_id' => $movie->id])->exists())
					$movie->link('languages', $language);
			}
		}

		if (isset($attributes->credits->cast) && is_array($attributes->credits->cast)) {
			foreach ($attributes->credits->cast as $castAttributes) {
				$person = Person::findOne($castAttributes->id);

				if ($person === null) {
					$person = new Person;
					$person->attributes = (array) $castAttributes;
					$person->save();
				}

				$cast = MovieCast::find()
					->where([
						'movie_id' => $movie->id,
						'person_id' => $person->id,
					])
					->one();

				if ($cast === null) {
					$cast = new MovieCast;
					$cast->attributes = (array) $castAttributes;
					$cast->id = null;
					$cast->movie_id = $movie->id;
					$cast->person_id = $castAttributes->id;
					$cast->save();
				}
			}
		}

		if (isset($attributes->credits->crew) && is_array($attributes->credits->crew)) {
			foreach ($attributes->credits->crew as $crewAttributes) {
				$person = Person::findOne($crewAttributes->id);

				if ($person === null) {
					$person = new Person;
					$person->attributes = (array) $crewAttributes;
					$person->save();
				}

				$crew = MovieCrew::find()
					->where([
						'movie_id' => $movie->id,
						'person_id' => $person->id,
					])
					->one();

				if ($crew === null) {
					$crew = new MovieCrew;
					$crew->attributes = (array) $crewAttributes;
					$crew->id = null;
					$crew->movie_id = $movie->id;
					$crew->person_id = $crewAttributes->id;
					$crew->save();
				}
			}
		}

		if (isset($attributes->videos->results) && is_array($attributes->videos->results)) {
			foreach ($attributes->videos->results as $videoAttributes) {
				$video = MovieVideo::findOne($videoAttributes->id);

				if ($video === null) {
					$video = new MovieVideo;
					$video->id = $videoAttributes->id;
					$video->movie_id = $movie->id;
					$video->key = $videoAttributes->key;
					$video->name = $videoAttributes->name;
					$video->site = $videoAttributes->site;
					$video->size = $videoAttributes->size;
					$video->type = $videoAttributes->type;

					$video->save();
				}
			}
		}

		if (!$movie->save()) {
			Yii::warning("Could update movie #{$movie->id} '" . serialize($movie->errors) . "': " . serialize($attributes), 'application\sync');
			return false;
		}

		if ($isSimilarMovie) {
			$similarMovie->similar_to_movie_id = $movie->id;
			$similarMovie->save();
		}

		return true;
	}

	public function syncPerson($person)
	{
		Yii::info("Syncing person #{$person->id}...", 'application\sync');

		$attributes = $this->getPerson($person);

		if ($attributes === false) {
			if (!empty($person->deleted_at)) {
				$person->delete();
			} else {
				$person->deleted_at = date('Y-m-d H:i:s');
				$person->save();
			}

			return false;
		}

		$person->attributes = (array) $attributes;
		if (!empty($person->birthday)) {
			$person->birthday = date('Y-m-d', strtotime($person->birthday));
		}
		if (!empty($person->deathday)) {
			$person->deathday = date('Y-m-d', strtotime($person->deathday));
		}

		$person->deleted_at = null;

		if (!$person->save()) {
			Yii::warning("Could update person {$person->id} '" . serialize($person->errors) . "': " . serialize($attributes), 'application\sync');
			return false;
		}

		return true;
	}

	public function syncTvChange($id)
	{
		Yii::info("Syncing tv show change #{$id}...", 'application\sync');

		$attributes = $this->getTvChange($id);

		if ($attributes === false)
			return false;

		$shows = Show::find()
			->where(['themoviedb_id' => $id])
			->with(['language'])
			->all();

		if (count($shows) == 0)
			return false;

		foreach ($attributes->changes as $attribute) {
			switch ($attribute->key) {
				case 'season':
					foreach ($attribute->items as $item) {
						switch ($item->action) {
							case 'created':
								foreach ($shows as $show) {
									$season = Season::find()
										->where([
											'show_id' => $show->id,
											'number' => $item->value->season_number,
										])
										->one();

									if ($season === null) {
										$season = new Season;
										$season->themoviedb_id = $item->value->season_id;
										$season->number = $item->value->season_number;
										$season->save();
										$season->link('show', $show);
									}
								}
								break;
							case 'added':
								foreach ($shows as $show) {
									$season = Season::find()
										->where([
											'show_id' => $show->id,
											'number' => $item->value->season_number,
										])
										->one();

									if ($season === null) {
										$season = new Season;
										$season->themoviedb_id = $item->value->season_id;
										$season->number = $item->value->season_number;
										$season->save();
										$season->link('show', $show);
									}
								}
								break;
							case 'updated':
								$this->syncSeasonChanges($item->value->season_id, $item->value->season_number);
								break;
							case 'destroyed':
								foreach ($shows as $show) {
									$seasons = Season::find()
										->where([
											'show_id' => array_map(function($show) {
												return $show->id;
											}, $shows),
											'number' => $item->value->season_number,
										])
										->all();

									foreach ($seasons as $season)
										$season->delete();
								}
								break;
							default:
								throw new \Exception('Unknown tv season item action ' . $item->action . ': ' . serialize([$id, $attribute]));
						}
					}
					break;
				case 'created_by':
					foreach ($attribute->items as $item) {
						switch ($item->action) {
							case 'added':
								$person = Person::findOne($item->value->person_id);

								if ($person === null) {
									$person = new Person;
									$person->id = $item->value->person_id;
									$person->save();

									foreach ($shows as $show)
										$show->link('creators', $person);

									break;
								}

								foreach ($shows as $show)
									if (!ShowCreator::find()->where(['person_id' => $person->id, 'show_id' => $show->id])->exists())
										$show->link('creators', $person);

								break;
							case 'deleted':
								$creators = ShowCreator::find()
									->where([
										'person_id' => $item->original_value->person_id,
										'show_id' => array_map(function($show) {
											return $show->id;
										}, $shows),
									])
									->all();

								foreach ($creators as $creator)
									$creator->delete();

								break;
							default:
								throw new \Exception('Unknown tv created_by item action ' . $item->action . ': ' . serialize([$id, $attribute]));
						}
					}
					break;
				case 'crew':
					foreach ($attribute->items as $item) {
						switch ($item->action) {
							case 'added':
								$person = Person::findOne($item->value->person_id);

								if ($person === null) {
									$person = new Person;
									$person->id = $item->value->person_id;
									$person->save();

									foreach ($shows as $show) {
										$crew = ShowCrew::find()
											->where([
												'show_id' => $show->id,
												'person_id' => $person->id,
											])
											->one();

										if ($crew === null) {
											$crew = new ShowCrew;
											$crew->show_id = $show->id;
											$crew->person_id = $person->id;
											$crew->save();
										}
									}

									break;
								}

								foreach ($shows as $show) {
									if (!ShowCrew::find()->where(['person_id' => $person->id, 'show_id' => $show->id])->exists()) {
										$crew = ShowCrew::find()
											->where([
												'show_id' => $show->id,
												'person_id' => $person->id,
											])
											->one();

										if ($crew === null) {
											$crew = new ShowCrew;
											$crew->show_id = $show->id;
											$crew->person_id = $person->id;
											$crew->save();
										}
									}
								}

								break;
							case 'updated':
								break;
							case 'deleted':
								$person = Person::findOne($item->original_value->person_id);

								if ($person !== null) {
									foreach ($shows as $show) {
										$crew = ShowCrew::find()
											->where([
												'show_id' => $show->id,
												'person_id' => $person->id,
											])
											->one();

										if ($crew !== null) {
											$crew->delete();
										}
									}
								}
								break;
							default:
								throw new \Exception('Unknown tv crew item action ' . $item->action . ': ' . serialize([$id, $attribute]));
						}
					}
					break;
				case 'cast':
					foreach ($attribute->items as $item) {
						switch ($item->action) {
							case 'added':
								$person = Person::findOne($item->value->person_id);

								if ($person === null) {
									$person = new Person;
									$person->id = $item->value->person_id;
									$person->save();

									foreach ($shows as $show)
										$show->link('cast', $person);

									break;
								}

								foreach ($shows as $show)
									if (!ShowCast::find()->where(['person_id' => $person->id, 'show_id' => $show->id])->exists())
										$show->link('cast', $person);

								break;
							default:
								throw new \Exception('Unknown tv cast item action ' . $item->action . ': ' . serialize([$id, $attribute]));
						}
					}
					break;
				case 'genres':
					foreach ($attribute->items as $item) {
						switch ($item->action) {
							case 'added':
								$genre = Genre::findOne($item->value->id);

								if ($genre === null) {
									$genre = new Genre;
									$genre->id = $item->value->id;
									$genre->name = $item->value->name;
									$genre->save();

									foreach ($shows as $show)
										$show->link('genres', $genre);

									break;
								}

								foreach ($shows as $show)
									if (!ShowGenre::find()->where(['genre_id' => $genre->id, 'show_id' => $show->id])->exists())
										$show->link('genres', $genre);

								break;
							case 'deleted':
								$showGenres = ShowGenre::find()
									->where([
										'genre_id' => $item->original_value->id,
										'show_id' => array_map(function($show) {
											return $show->id;
										}, $shows),
									])
									->all();

								foreach ($showGenres as $showGenre)
									$showGenre->delete();

								break;
							default:
								throw new \Exception('Unknown tv genres item action ' . $item->action . ': ' . serialize([$id, $attribute]));
						}
					}
					break;
				case 'origin_country':
					foreach ($attribute->items as $item) {
						switch ($item->action) {
							case 'deleted':
								$country = Country::findOne([
									'name' => $item->original_value,
								]);

								$showCountries = ShowCountry::find()
									->where([
										'country_id' => $country->id,
										'show_id' => array_map(function($show) {
											return $show->id;
										}, $shows),
									])
									->all();

								foreach ($showCountries as $showCountry)
									$showCountry->delete();

								break;
							case 'added':
								$country = Country::findOne([
									'name' => $item->value,
								]);

								if ($country === null) {
									$country = new Country;
									$country->name = $item->value;
									$country->save();
								}

								foreach ($shows as $show) {
									$showCountries = ShowCountry::find()
										->where([
											'country_id' => $country->id,
											'show_id' => $show->id
										])
										->all();

									foreach ($showCountries as $showCountry)
										$showCountry->delete();
								}

								break;
							default:
								throw new \Exception('Unknown tv origin_country item action ' . $item->action . ': ' . serialize([$id, $attribute]));
						}
					}

					break;
				case 'network':
					foreach ($attribute->items as $item) {
						switch ($item->action) {
							case 'added':
								$network = Network::findOne($item->value->id);

								if ($network === null) {
									$network = new Network;
									$network->id = $item->value->id;
									$network->name = $item->value->name;
									$network->save();

									foreach ($shows as $show)
										$show->link('networks', $network);

									break;
								}

								foreach ($shows as $show)
									if (!ShowNetwork::find()->where(['network_id' => $network->id, 'show_id' => $show->id])->exists())
										$show->link('networks', $network);

								break;
							case 'deleted':
								$network = Network::findOne($item->original_value->id);

								if ($network !== null) {
									foreach ($shows as $show) {
										$showNetworks = ShowNetwork::find()
											->where([
												'network_id' => $network->id,
												'show_id' => $show->id
											])
											->all();

										foreach ($showNetworks as $showNetwork) {
											$showNetwork->delete();
										}
									}
								}

								break;
							default:
								throw new \Exception('Unknown tv network item action ' . $item->action . ': ' . serialize([$id, $attribute]));
						}
					}

					break;
				case 'videos':
					foreach ($attribute->items as $item) {
						switch ($item->action) {
							case 'added':
								$video = ShowVideo::findOne($item->value->id);

								if ($video === null) {
									$video = new ShowVideo;
									$video->id = $item->value->id;
									$video->key = $item->value->key;
									$video->name = $item->value->name;
									$video->site = isset($item->value->site) ? $item->value->site : null;
									$video->size = $item->value->size;
									$video->type = $item->value->type;

									foreach ($shows as $show) {
										if ($show->language->iso == $item->iso_639_1) {
											$video->show_id = $show->id;
										}
									}

									break;
								}

								break;
							case 'updated':
								$video = ShowVideo::findOne($item->original_value->id);
								if ($video === null) {
									$video = new ShowVideo;
								}

								$video->id = $item->value->id;
								$video->key = $item->value->key;
								$video->name = $item->value->name;
								$video->site = isset($item->value->site) ? $item->value->site : null;
								$video->size = $item->value->size;
								$video->type = $item->value->type;
								$video->save();

								break;
							case 'deleted':
								$video = ShowVideo::findOne($item->original_value->id);

								if ($video !== null)
									$video->delete();

								break;
							default:
								throw new \Exception('Unknown tv video item action ' . $item->action . ': ' . serialize([$id, $attribute]));
						}
					}

					break;
				case 'production_countries':
				case 'plot_keywords':
				case 'translations':
				case 'languages':
				case 'images':
				case 'season_regular':
				case 'certifications':
				case 'fanhattan_id':
				case 'tvrage_id':
				case 'imdb_id':
				case 'tvdb_id':
				case 'freebase_mid':
				case 'freebase_id':
				case 'guest_stars':
				case 'alternative_titles':
				case 'general':
				case 'episode_run_time':
				case 'production_companies':
				case 'type':
				case 'iso_639_1':
					break;
				case 'overview':
					foreach ($attribute->items as $item) {
						foreach ($shows as $show) {
							if ($show->language->iso == $item->iso_639_1) {
								$show->overview = isset($item->value) ? $item->value : null;
								$show->save();
							}
						}
					}
					break;
				case 'name':
					foreach ($attribute->items as $item) {
						foreach ($shows as $show) {
							if ($show->language->iso == $item->iso_639_1) {
								$show->name = isset($item->value) ? $item->value : null;
								$show->save();
							}
						}
					}
					break;
				case 'original_name':
					foreach ($attribute->items as $item) {
						foreach ($shows as $show) {
							$show->original_name = isset($item->value) ? $item->value : null;
							$show->save();
						}
					}
					break;
				case 'backdrop_path':
					foreach ($attribute->items as $item) {
						foreach ($shows as $show) {
							if ($show->language->iso == $item->iso_639_1) {
								$show->backdrop_path = isset($item->value) ? $item->value : null;
								$show->save();
							}
						}
					}
					break;
				case 'poster_path':
					foreach ($attribute->items as $item) {
						foreach ($shows as $show) {
							if ($show->language->iso == $item->iso_639_1) {
								$show->poster_path = isset($item->value) ? $item->value : null;
								$show->save();
							}
						}
					}
					break;
				case 'first_air_date':
					foreach ($attribute->items as $item) {
						foreach ($shows as $show) {
							$show->first_air_date = isset($item->value) ? $item->value : null;
							$show->save();
						}
					}
					break;
				case 'last_air_date':
					foreach ($attribute->items as $item) {
						foreach ($shows as $show) {
							$show->last_air_date = isset($item->value) ? $item->value : null;
							$show->save();
						}
					}
					break;
				case 'homepage':
					foreach ($attribute->items as $item) {
						foreach ($shows as $show) {
							if ($show->language->iso == $item->iso_639_1) {
								$show->homepage = isset($item->value) ? $item->value : null;
								$show->save();
							}
						}
					}
					break;
				case 'in_production':
					foreach ($attribute->items as $item) {
						foreach ($shows as $show) {
							if ($show->language->iso == $item->iso_639_1) {
								$show->in_production = isset($item->value) ? $item->value : null;
								$show->save();
							}
						}
					}
					break;
				case 'popularity':
					foreach ($attribute->items as $item) {
						foreach ($shows as $show) {
							if ($show->language->iso == $item->iso_639_1) {
								$show->popularity = isset($item->value) ? $item->value : null;
								$show->save();
							}
						}
					}
					break;
				case 'status':
					foreach ($attribute->items as $item) {
						foreach ($shows as $show) {
							$show->status = isset($item->value) ? $item->value : null;
							$show->save();
						}
					}
					break;
				case 'vote_average':
					foreach ($attribute->items as $item) {
						foreach ($shows as $show) {
							$show->vote_average = isset($item->value) ? $item->value : null;
							$show->save();
						}
					}
					break;
				case 'vote_count':
					foreach ($attribute->items as $item) {
						foreach ($shows as $show) {
							$show->vote_count = isset($item->value) ? $item->value : null;
							$show->save();
						}
					}
					break;
				case 'episode':
					foreach ($attribute->items as $item) {
						$this->syncEpisodeChanges($item->value->episode_id, $item->value->episode_number);
					}

					break;
				default:
					throw new \Exception('Unknown tv attribute key ' . $attribute->key . ': ' . serialize([$id, $attribute]));
			}
		}
	}

	public function syncSeasonChanges($id, $number)
	{
		Yii::info("Syncing season change #{$id}...", 'application\sync');

		$attributes = $this->getSeasonChanges($id);

		if ($attributes === false)
			return false;

		$seasons = Season::find()
			->where([
				'themoviedb_id' => $id,
				'number' => $number,
			])
			->with([
				'show',
				'show.language'
			])
			->all();

		if (count($seasons) == 0)
			return false;

		foreach ($attributes->changes as $attribute) {
			switch ($attribute->key) {
				case 'episode':
					foreach ($attribute->items as $item) {
						switch ($item->action) {
							case 'updated':
								$this->syncEpisodeChanges($item->value->episode_id);
								break;
							case 'created':
								foreach ($seasons as $season) {
									$episode = Episode::find()
										->where([
											'number' => $item->value->episode_number,
											'season_id' => $season->id,
										])
										->one();

									if ($episode === null) {
										$episode = new Episode;
										$episode->number = $item->value->episode_number;
										$episode->themoviedb_id = $item->value->episode_id;

										$episode->save();
										$episode->link('season', $season);
									} else if ($episode->themoviedb_id == 0) {
										$episode->themoviedb_id = $item->value->episode_id;
										$episode->save();
									}
								}
								break;
							case 'added':
								foreach ($seasons as $season) {
									$episodeExists = Episode::find()
										->where([
											'number' => $item->value->episode_number,
											'season_id' => $season->id,
										])
										->exists();

									if (!$episodeExists) {
										$episode = new Episode;
										$episode->number = $item->value->episode_number;

										$episode->save();
										$episode->link('season', $season);
									}
								}
								break;
							case 'deleted':
								$episodes = Episode::find()
									->where([
										'season_id' => array_map(function($season) {
											return $season->id;
										}, $seasons),
										'number' => $item->original_value->episode_number,
									])
									->all();

								foreach ($episodes as $episode)
									$episode->delete();

								break;
							case 'destroyed':
								$episodes = Episode::find()
									->where([
										'themoviedb_id' => $item->value->episode_id,
										'number' => $item->value->episode_number,
									])
									->all();

								foreach ($episodes as $episode)
									$episode->delete();

								break;
							default:
								throw new \Exception('Unknown season episode item action ' . $item->action . ': ' . serialize([$id, $attribute]));
						}
					}
					break;
				case 'season_number':
					foreach ($attribute->items as $item) {
						switch ($item->action) {
							case 'added':
								foreach ($seasons as $season) {
									if ($season->number != $item->value) {
										$season->number = $item->value;
										$season->save();
									}
								}

								break;
							default:
								throw new \Exception('Unknown season season_number item action ' . $item->action . ': ' . serialize([$id, $attribute]));
						}
					}

					break;
				case 'overview':
					foreach ($attribute->items as $item) {
						foreach ($seasons as $season) {
							if ($season->show->language->iso == $item->iso_639_1) {
								$season->overview = isset($item->value) ? $item->value : null;
								$season->save();
							}
						}
					}
					break;
				case 'air_date':
					foreach ($attribute->items as $item) {
						foreach ($seasons as $season) {
							$season->air_date = isset($item->value) ? $item->value : null;
							$season->save();
						}
					}
					break;
				case 'name':
					foreach ($attribute->items as $item) {
						foreach ($seasons as $season) {
							if ($season->show->language->iso == $item->iso_639_1) {
								$season->name = isset($item->value) ? $item->value : null;
								$season->save();
							}
						}
					}
					break;
				case 'poster_path':
					foreach ($attribute->items as $item) {
						foreach ($seasons as $season) {
							if ($season->show->language->iso == $item->iso_639_1) {
								$season->poster_path = isset($item->value) ? $item->value : null;
								$season->save();
							}
						}
					}
					break;
				case 'images':
				case 'videos':
				case 'general':
				case 'freebase_mid':
				case 'tvdb_id':
				case 'tvrage_id':
					break;
				default:
					throw new \Exception('Unknown season attribute key ' . $attribute->key . ': ' . serialize([$id, $attribute]));
			}
		}
	}

	public function syncEpisodeChanges($id)
	{
		Yii::info("Syncing episode change #{$id}...", 'application\sync');

		$attributes = $this->getEpisodeChanges($id);

		if ($attributes === false)
			return false;

		$episodes = Episode::find()
			->where([
				'themoviedb_id' => $id,
			])
			->with([
				'season.show.language'
			])
			->all();

		if (count($episodes) == 0)
			return false;

		foreach ($attributes->changes as $attribute) {
			switch ($attribute->key) {
				case 'videos':
				case 'guest_stars':
				case 'crew':
				case 'imdb_id':
				case 'tvdb_id':
				case 'freebase_mid':
				case 'tvrage_id':
				case 'freebase_id':
				case 'general':
					break;
				case 'images':
					foreach ($attribute->items as $item) {
						switch ($item->action) {
							case 'added':
							case 'updated':
								if (isset($item->value->still->file_path)) {
									foreach ($episodes as $episode) {
										$episode->still_path = $item->value->still->file_path;
										$episode->save();
									}
								}
								break;
							case 'deleted':
								foreach ($episodes as $episode) {
									$episode->still_path = null;
									$episode->save();
								}
								break;
							default:
								throw new \Exception('Unknown episode images item action ' . $item->action . ': ' . serialize([$id, $attribute]));
						}
					}
					break;
				case 'name':
					foreach ($attribute->items as $item) {
						foreach ($episodes as $episode) {
							if ($episode->season->show->language->iso == $item->iso_639_1) {
								$episode->name = isset($item->value) ? $item->value : null;
								$episode->save();
							}
						}
					}
					break;
				case 'overview':
					foreach ($attribute->items as $item) {
						foreach ($episodes as $episode) {
							if ($episode->season->show->language->iso == $item->iso_639_1) {
								$episode->overview = isset($item->value) ? $item->value : null;
								$episode->save();
							}
						}
					}
					break;
				case 'still_path':
					foreach ($attribute->items as $item) {
						foreach ($episodes as $episode) {
							if ($episode->season->show->language->iso == $item->iso_639_1) {
								$episode->still_path = isset($item->value) ? $item->value : null;
								$episode->save();
							}
						}
					}
					break;
				case 'vote_average':
					foreach ($attribute->items as $item) {
						foreach ($episodes as $episode) {
							$episode->vote_average = isset($item->value) ? $item->value : null;
							$episode->save();
						}
					}
					break;
				case 'vote_count':
					foreach ($attribute->items as $item) {
						foreach ($episodes as $episode) {
							$episode->vote_count = isset($item->value) ? $item->value : null;
							$episode->save();
						}
					}
					break;
				case 'production_code':
					foreach ($attribute->items as $item) {
						foreach ($episodes as $episode) {
							$episode->production_code = isset($item->value) ? $item->value : null;
							$episode->save();
						}
					}
					break;
				case 'air_date':
					foreach ($attribute->items as $item) {
						foreach ($episodes as $episode) {
							$episode->air_date = isset($item->value) ? $item->value : null;
							$episode->save();
						}
					}
					break;
				case 'season_number':
					foreach ($attribute->items as $item) {
						switch ($item->action) {
							case 'added':
								foreach ($episodes as $episode) {
									$season = Season::find()
										->where([
											'show_id' => $episode->season->show_id,
											'number' => $item->value,
										])
										->one();

									if ($season === null) {
										$season = new Season;
										$season->show_id = $episode->season->show_id;
										$season->number = $item->value;
										$season->save();
									}

									$episode->season_id = $season->id;
									$episode->save();
								}
								break;
							default:
								throw new \Exception('Unknown episode season_number item action ' . $item->action . ': ' . serialize([$id, $attribute]));
						}
					}

					break;
				case 'episode_number':
					foreach ($attribute->items as $item) {
						switch ($item->action) {
							case 'updated':
								foreach ($episodes as $episode) {
									Yii::$app->db->createCommand('
										DELETE FROM
											{{%episode}}
										WHERE
											[[number]] = :number AND
											[[season_id]] = :season_id
									', [
										'number' => $item->value,
										'season_id' => $episode->season_id,
									])
										->execute();
								}

								foreach ($episodes as $episode) {
									$episode->number = isset($item->value) ? $item->value : null;
									$episode->save();
								}

								break;
							case 'added':
								foreach ($episodes as $episode) {
									$exists = Episode::find()
										->where([
											'season_id' => $episode->season_id,
											'number' => $item->value,
										])
										->exists();

									if (!$exists) {
										$newEpisode = new Episode;
										$newEpisode->season_id = $episode->season_id;
										$newEpisode->number = $item->value;
										$newEpisode->save();
									}
								}

								break;
							default:
								throw new \Exception('Unknown episode episode_number item action' . $item->action . ': ' . serialize([$id, $attribute]));
						}
					}
					break;
				default:
					throw new \Exception('Unknown episode attribute key ' . $attribute->key . ': ' . serialize([$id, $attribute]));
			}
		}
	}

	/**
	 * Create a new request token.
	 *
	 * @return string
	 */
	public function createRequestToken()
	{
		$response = $this->get('/authentication/token/new');
		if ($response !== false && $response->success === true)
			return $response->request_token;
		else
			return null;
	}

	/**
	 * Get session ID from request token.
	 *
	 * @param string $requestToken
	 *
	 * @return string
	 */
	public function createSessionID($requestToken)
	{
		$response = $this->get('/authentication/session/new', [
			'request_token' => $requestToken,
		]);
		if ($response !== false && $response->success === true)
			return $response->session_id;
		else
			return null;
	}

	/**
	 * Get the account ID for the session ID.
	 *
	 * @param string $sessionId
	 *
	 * @return int
	 */
	public function getAccountId($sessionId)
	{
		$response = $this->get('/account', [
			'session_id' => $sessionId,
		]);
		if ($response !== false && is_integer($response->id))
			return $response->id;
		else
			return null;
	}

	/**
	 * Sync ratings with themoviedb.
	 *
	 * @param \app\models\User $user
	 *
	 * @return bool
	 */
	public function syncUserRatings($user)
	{
		if (!$user->hasTheMovieDBAccount())
			return true;

		Yii::info("Syncing ratings for user #{$user->id}...", 'application\sync');

		$successMovie = $this->syncMovieRatings($user);
		$successTv = $this->syncTvRatings($user);

		// Sync movies rated locally
		$movieRatings = UserMovieRating::find()
			->where(['user_id' => $user->id])
			->where(['sync' => false])
			->all();
		foreach ($movieRatings as $movieRating) {
			$this->rateMovie($user, $movieRating->themoviedb_id, $movieRating->rating);
			$movieRating->sync = true;
			$movieRating->save();
		}

		// Sync tv shows rated locally
		$showRatings = UserShowRating::find()
			->where(['user_id' => $user->id])
			->where(['sync' => false])
			->all();
		foreach ($showRatings as $showRating) {
			$this->rateTv($user, $showRating->themoviedb_id, $showRating->rating);
			$showRating->sync = true;
			$showRating->save();
		}

		return ($successMovie && $successTv);
	}

	/**
	 * Only sync local movie ratings with themoviedb.org.
	 *
	 * @param \app\models\User $user
	 *
	 * @return bool
	 */
	protected function syncMovieRatings($user)
	{
		// Sync movies rated at themoviedb.org
		$results = $this->paginate(sprintf('/account/%d/rated/movies', $user->themoviedb_account_id), [
			'session_id' => $user->themoviedb_session_id,
			'language' => $user->language->iso,
		]);

		if ($results === false)
			return false;

		foreach ($results as $movieRating) {
			// Search for rated movie
			$movie = Movie::find()
				->where(['themoviedb_id' => $movieRating->id])
				->andWhere(['language_id' => $user->language->id])
				->one();

			if ($movie === null) {
				// Create new movie
				$movie = new Movie;
				$movie->themoviedb_id = $movieRating->id;
				$movie->language_id = $user->language->id;
				$movie->adult = $movieRating->adult;
				$movie->backdrop_path = $movieRating->backdrop_path;
				$movie->original_title = $movieRating->original_title;
				$movie->release_date = $movieRating->release_date;
				$movie->poster_path = $movieRating->poster_path;
				$movie->popularity = $movieRating->popularity;
				$movie->title = $movieRating->title;
				$movie->vote_average = $movieRating->vote_average;
				$movie->vote_count = $movieRating->vote_count;
				if (!$movie->save())
					return false;
			}

			$rating = UserMovieRating::find()
				->where(['user_id' => $user->id])
				->where(['themoviedb_id' => $movieRating->id])
				->one();

			if ($rating === null) {
				// Create rating
				$rating = new UserMovieRating;
				$rating->user_id = $user->id;
				$rating->themoviedb_id = $movieRating->id;
				$rating->rating = $movieRating->rating;
				$rating->sync = true;
				$rating->save();
			} else if ($rating->rating != $movieRating->rating ||
				$rating->sync === false)
			{
				$rating->rating = $movieRating->rating;
				$rating->sync = true;
				$rating->save();
			}
		}
	}

	/**
	 * Only sync local tv ratings with themoviedb.org.
	 *
	 * @param \app\models\User $user
	 *
	 * @return bool
	 */
	protected function syncTvRatings($user)
	{
		// Sync movies rated at themoviedb.org
		$results = $this->paginate(sprintf('/account/%d/rated/tv', $user->themoviedb_account_id), [
			'session_id' => $user->themoviedb_session_id,
			'language' => $user->language->iso,
		]);

		if ($results === false)
			return false;

		foreach ($results as $showRating) {
			// Search for rated show
			$show = Show::find()
				->where(['themoviedb_id' => $showRating->id])
				->andWhere(['language_id' => $user->language->id])
				->one();

			if ($show === null) {
				// Create new show
				$show = new Show;
				$show->themoviedb_id = $showRating->id;
				$show->language_id = $user->language->id;
				$show->backdrop_path = $showRating->backdrop_path;
				$show->original_name = $showRating->original_name;
				$show->first_air_date = $showRating->first_air_date;
				$show->poster_path = $showRating->poster_path;
				$show->popularity = $showRating->popularity;
				$show->name = $showRating->name;
				$show->vote_average = $showRating->vote_average;
				$show->vote_count = $showRating->vote_count;
				if (!$show->save())
					return false;
			}

			$rating = UserShowRating::find()
				->where(['user_id' => $user->id])
				->where(['themoviedb_id' => $showRating->id])
				->one();

			if ($rating === null) {
				// Create rating
				$rating = new UserShowRating;
				$rating->user_id = $user->id;
				$rating->themoviedb_id = $showRating->id;
				$rating->rating = $showRating->rating;
				$rating->sync = true;
				$rating->save();
			} else if ($rating->rating != $showRating->rating ||
				$rating->sync === false)
			{
				$rating->rating = $showRating->rating;
				$rating->sync = true;
				$rating->save();
			}
		}
	}

	/**
	 * Save a movie rating for a user at themoviedb.org.
	 *
	 * @param \app\models\User $user
	 * @param int $themoviedbId
	 * @param int $rating
	 *
	 * @return bool
	 */
	public function rateMovie($user, $themoviedbId, $rating)
	{
		$result = $this->post(sprintf('/movie/%d/rating', $themoviedbId), [
			'session_id' => $user->themoviedb_session_id,
		], [
			'value' => $rating,
		]);

		if ($result !== false && isset($result->status_code) && ($result->status_code === 12 || $result->status_code === 1))
			return true;
		else
			return false;
	}

	/**
	 * Save a tv show rating for a user at themoviedb.org.
	 *
	 * @param \app\models\User $user
	 * @param int $themoviedbId
	 * @param int $rating
	 *
	 * @return bool
	 */
	public function rateTv($user, $themoviedbId, $rating)
	{
		$result = $this->post(sprintf('/tv/%d/rating', $themoviedbId), [
			'session_id' => $user->themoviedb_session_id,
		], [
			'value' => $rating,
		]);

		if ($result !== false && isset($result->status_code) && ($result->status_code === 12 || $result->status_code === 1))
			return true;
		else
			return false;
	}
}
