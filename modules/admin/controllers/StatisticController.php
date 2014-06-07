<?php namespace app\modules\admin\controllers;

use \Yii;
use \yii\filters\AccessControl;

class StatisticController extends BaseController
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['index', 'loadUserActionTimeline', 'loadApiCallTimeline', 'loadPopularTv', 'loadPopularMovie'],
				'rules' => [
					[
						'actions' => ['index', 'loadUserActionTimeline', 'loadApiCallTimeline', 'loadPopularTv', 'loadPopularMovie'],
						'allow' => true,
						'roles' => ['admin'],
					],
				],
			],
		];
	}

	public function actionIndex()
	{
		return $this->render('index');
	}

	public function actionLoadUserActionTimeline()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$cacheId = 'statistic-user-action-timeline';

		$dates = Yii::$app->cache->get($cacheId);

		if ($dates === false) {
			$timestamps = [];
			for ($i = 0; $i < 30; $i++) {
				$timestamps[] = mktime(0, 0, 0, date('n'), date('j') - $i, date('Y')) * 1000;
			}

			$moviesWatched = Yii::$app->db->createCommand('
				SELECT
					DATE({{%user_movie}}.[[created_at]]) AS [[x]],
					COUNT(*) AS [[y]]
				FROM
					{{%user_movie}}
				WHERE
					{{%user_movie}}.[[created_at]] > :time
				GROUP BY
					DATE({{%user_movie}}.[[created_at]])
				ORDER BY
					{{%user_movie}}.[[created_at]] ASC
			', [
				':time' => date('Y-m-d H:i:s', (time() - 3600 * 24 * 30))
			])->queryAll();

			$dates['moviesWatched'] = array_map(function($movie) {
				return [
					'x' => strtotime($movie['x']) * 1000,
					'y' => intval($movie['y']),
				];
			}, $moviesWatched);

			$moviesWatchlist = Yii::$app->db->createCommand('
				SELECT
					DATE({{%user_movie_watchlist}}.[[created_at]]) AS [[x]],
					COUNT(*) AS [[y]]
				FROM
					{{%user_movie_watchlist}}
				WHERE
					{{%user_movie_watchlist}}.[[created_at]] > :time
				GROUP BY
					DATE({{%user_movie_watchlist}}.[[created_at]])
				ORDER BY
					{{%user_movie_watchlist}}.[[created_at]] ASC
			', [
				':time' => date('Y-m-d H:i:s', (time() - 3600 * 24 * 30))
			])->queryAll();

			$dates['moviesWatchlist'] = array_map(function($movie) {
				return [
					'x' => strtotime($movie['x']) * 1000,
					'y' => intval($movie['y']),
				];
			}, $moviesWatchlist);

			$showsSubscribed = Yii::$app->db->createCommand('
				SELECT
					DATE({{%user_show}}.[[created_at]]) AS [[x]],
					COUNT(*) AS [[y]]
				FROM
					{{%user_show}}
				WHERE
					{{%user_show}}.[[created_at]] > :time
				GROUP BY
					DATE({{%user_show}}.[[created_at]])
				ORDER BY
					{{%user_show}}.[[created_at]] ASC
			', [
				':time' => date('Y-m-d H:i:s', (time() - 3600 * 24 * 30))
			])->queryAll();

			$dates['showsSubscribed'] = array_map(function($show) {
				return [
					'x' => strtotime($show['x']) * 1000,
					'y' => intval($show['y']),
				];
			}, $showsSubscribed);

			$episodesWatched = Yii::$app->db->createCommand('
				SELECT
					DATE({{%user_episode}}.[[created_at]]) AS [[x]],
					COUNT(*) AS [[y]]
				FROM
					{{%user_episode}}
				WHERE
					{{%user_episode}}.[[created_at]] > :time
				GROUP BY
					DATE({{%user_episode}}.[[created_at]])
				ORDER BY
					{{%user_episode}}.[[created_at]] ASC
			', [
				':time' => date('Y-m-d H:i:s', (time() - 3600 * 24 * 30))
			])->queryAll();

			$dates['episodesWatched'] = array_map(function($episode) {
				return [
					'x' => strtotime($episode['x']) * 1000,
					'y' => intval($episode['y']),
				];
			}, $episodesWatched);

			foreach ($timestamps as $timestamp) {
				foreach ($dates as $key => $data) {
					foreach ($data as $dataPoint) {
						if ($dataPoint['x'] == $timestamp)
							continue 2;
					}

					$dates[$key][] = [
						'x' => $timestamp,
						'y' => 0,
					];
				}
			}

			foreach ($dates as $key => $data) {
				usort($dates[$key], function($a, $b) {
					if ($a['x'] == $b['x']) {
				        return 0;
				    }

				    return ($a['x'] < $b['x']) ? -1 : 1;
				});
			}

			Yii::$app->cache->set($cacheId, $dates, 3600);
		}

		return [
			[
				'name' => Yii::t('Statistic', 'Watched Movies'),
				'data' => $dates['moviesWatched'],
			],
			[
				'name' => Yii::t('Statistic', 'Movie Watchlist'),
				'data' => $dates['moviesWatchlist'],
			],
			[
				'name' => Yii::t('Statistic', 'Subscribed Shows'),
				'data' => $dates['showsSubscribed'],
			],
			[
				'name' => Yii::t('Statistic', 'Watched Episodes'),
				'data' => $dates['episodesWatched'],
			],
		];
	}

	public function actionLoadApiCallTimeline()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$cacheId = 'statistic-api-call-timeline';

		$dates = Yii::$app->cache->get($cacheId);

		if ($dates === false) {
			$timestamps = [];
			for ($i = 0; $i < 30; $i++) {
				$timestamps[] = mktime(0, 0, 0, date('n'), date('j') - $i, date('Y')) * 1000;
			}

			$apiCalls = Yii::$app->db->createCommand('
				SELECT
					DATE({{%themoviedb_rate}}.[[created_at]]) AS [[x]],
					COUNT(*) AS [[y]]
				FROM
					{{%themoviedb_rate}}
				WHERE
					{{%themoviedb_rate}}.[[created_at]] > :time
				GROUP BY
					DATE({{%themoviedb_rate}}.[[created_at]])
				ORDER BY
					{{%themoviedb_rate}}.[[created_at]] ASC
			', [
				':time' => date('Y-m-d H:i:s', (time() - 3600 * 24 * 30))
			])->queryAll();

			$data = array_map(function($calls) {
				return [
					'x' => strtotime($calls['x']) * 1000,
					'y' => intval($calls['y']),
				];
			}, $apiCalls);

			foreach ($timestamps as $timestamp) {
				foreach ($data as $dataPoint) {
					if ($dataPoint['x'] == $timestamp)
						continue 2;
				}

				$data[] = [
					'x' => $timestamp,
					'y' => 0,
				];
			}

			usort($data, function($a, $b) {
				if ($a['x'] == $b['x']) {
					return 0;
				}

				return ($a['x'] < $b['x']) ? -1 : 1;
			});

			Yii::$app->cache->set($cacheId, $dates, 3600);
		}

		return [
			[
				'name' => Yii::t('Statistic', 'API calls'),
				'data' => $data,
			],
		];
	}

	public function actionLoadPopularTv()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		$cacheId = 'statistic-popular-tv';
		$tvshows = Yii::$app->cache->get($cacheId);

		if ($tvshows === false) {
			$tvshows = Yii::$app->db->createCommand('
				SELECT
					{{%show}}.[[original_name]] as [[name]],
					COUNT({{%show}}.[[id]]) as [[y]]
				FROM
					{{%show}},
					{{%user_show}}
				WHERE
					{{%show}}.[[id]] = {{%user_show}}.[[show_id]]
				GROUP BY
					{{%show}}.[[themoviedb_id]]
				ORDER BY
					[[y]] DESC
				LIMIT
					20
			')->queryAll();

			$tvshows = array_map(function($show) {
				return [
					'name' => $show['name'],
					'y' => intval($show['y']),
				];
			}, $tvshows);

			Yii::$app->cache->set($cacheId, $tvshows, 3600);
		}

		return [[
			'name' => Yii::t('Statistic', 'Number of subscribers'),
			'data' => $tvshows,
		]];
	}

	public function actionLoadPopularMovie()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		$cacheId = 'statistic-popular-movie';
		$movies = Yii::$app->cache->get($cacheId);

		if ($movies === false) {
			$movies = Yii::$app->db->createCommand('
				SELECT
					{{%movie}}.[[original_title]] as [[name]],
					COUNT({{%movie}}.[[id]]) as [[y]]
				FROM
					{{%movie}},
					{{%user_movie}}
				WHERE
					{{%movie}}.[[id]] = {{%user_movie}}.[[movie_id]]
				GROUP BY
					{{%movie}}.[[themoviedb_id]]
				ORDER BY
					[[y]] DESC
				LIMIT
					20
			')->queryAll();

			$movies = array_map(function($movie) {
				return [
					'name' => $movie['name'],
					'y' => intval($movie['y']),
				];
			}, $movies);

			Yii::$app->cache->set($cacheId, $movies, 3600);
		}

		return [[
			'name' => Yii::t('Statistic', 'Number of watches'),
			'data' => $movies,
		]];
	}
}
