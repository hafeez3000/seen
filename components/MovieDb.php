<?php namespace app\components;

use \Yii;

use \Tmdb\ApiToken;
use \Tmdb\Client;

class MovieDb
{
	private $key = '';
	private $cache = 0;

	protected $errors = [];

	public function __construct()
	{
		$this->key = Yii::$app->params['themoviedb']['key'];
	}

	protected function get($path, $parameters)
	{
		$this->cache++;

		if ($this->cache == 30) {
			sleep(10);
			$this->cache = 1;
		}

		$parameters = array_merge_recursive($parameters, [
			'api_key' => $this->key,
		]);
		$url = Yii::$app->params['themoviedb']['url'] . $path . '?' . http_build_query($parameters);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);

		$response = curl_exec($curl);
		if ($response === false) {
			Yii::error("Error while requesting {$path}");
			$this->errors[] = "Error while requesting {$path}";
			return false;
		}

		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ($status >= 400) {
			Yii::error("Error while requesting {$url}, code #{$status}: " . $response);
			$this->errors[] = "Error while requesting {$path}, code #{$status}: " . $response;
			return false;
		}

		$result = json_decode($response);
		curl_close($curl);

		return $result;
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
			'append_to_response' => 'credits',
		]);
	}

	public function getSeason($season)
	{
		return $this->get(sprintf('/tv/%s/season/%s', $season->show->themoviedb_id, $season->number), [
			'language' => $season->show->language->iso,
		]);
	}

	public function getEpisode($episode)
	{
		return $this->get(sprintf('/tv/%s/season/%s/episode/%s', $episode->season->show->themoviedb_id, $episode->season->number, $episode->number), [
			'language' => $episode->season->show->language->iso,
		]);
	}
}