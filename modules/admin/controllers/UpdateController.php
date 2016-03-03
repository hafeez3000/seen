<?php namespace app\modules\admin\controllers;

use \Yii;
use \yii\filters\AccessControl;

use \app\modules\admin\controllers\BaseController;
use \app\models\Show;
use \app\models\Season;
use \app\models\Episode;
use \app\models\Movie;
use \app\models\MovieSimilar;
use \app\models\Language;
use \app\models\Person;
use \app\models\SyncStatus;
use \app\components\MovieDb;
use \app\components\LanguageHelper;

class UpdateController extends BaseController
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['index', 'count'],
				'rules' => [
					[
						'actions' => ['index', 'count'],
						'allow' => true,
						'roles' => ['viewUpdates'],
					],
				],
			],
		];
	}

	public function actionIndex()
	{
		// Get cronjob list
		if (!defined('YII_ENV') || YII_ENV != 'dev') {
			$line = exec("crontab -l", $cronjobs, $status);
		} else {
			$cronjobs = [
				'MAILTO="thelfensdrfer@gmail.com"',
				'',
				'# m h  dom mon dow   command',
				'# SEEN',
				'0	0	*	*	*	/var/www/seenapp.com/yii-cron "php /var/www/seenapp.com/main/yii sync/shows"',
				'0       1       *       *       *       /var/www/seenapp.com/yii-cron "php /var/www/seenapp.com/main/yii sync/seasons"',
				'0       2       *       *       *       /var/www/seenapp.com/yii-cron "php /var/www/seenapp.com/main/yii sync/episodes"',
				'0       3       *       *       *       /var/www/seenapp.com/yii-cron "php /var/www/seenapp.com/main/yii sync/movies"',
				'0	4	*	*	*	/var/www/seenapp.com/yii-cron "php /var/www/seenapp.com/main/yii sync/movies-changes"',
				'0	5	*	*	*	/var/www/seenapp.com/yii-cron "php /var/www/seenapp.com/main/yii sync/movies-similar"',
				'0	6	*	*	*	/var/www/seenapp.com/yii-cron "php /var/www/seenapp.com/main/yii sync/popular-movies"',
				'0       7       *       *       *       /var/www/seenapp.com/yii-cron "php /var/www/seenapp.com/main/yii sync/popular-shows"',
				'0       8       *       *       *       /var/www/seenapp.com/yii-cron "php /var/www/seenapp.com/main/yii sync/persons"',
				'0	9	*	*	*	/var/www/seenapp.com/yii-cron "php /var/www/seenapp.com/main/yii sync/tv-changes"',
			];
		}

		// Filter yii/sync commands
		$cronjobs = array_filter($cronjobs, function($cronjob) {
			if (strpos($cronjob, '#') === 0)
				return false;

			if (strpos($cronjob, 'yii sync/') === false)
				return false;

			return preg_match('/(\d+)\s+(\d+)\s+\*\s+\*\s+\*\s+.*yii (sync\/.*)/', $cronjob) === 1;
		});

		// Parse hour, minute and command
		$cronjobs = array_map(function($raw) {
			preg_match('/(\d+)\s+(\d+)\s+\*\s+\*\s+\*\s+.*yii (sync\/.*)"/', $raw, $matches);

			return [
				'minute' => $matches[1],
				'hour' => $matches[2],
				'command' => $matches[3],
			];
		}, $cronjobs);

		// Sort by execution time
		usort($cronjobs, function($a, $b) {
			if ($a['hour'] == $b['hour'] && $a['minute'] == $b['minute'])
				return 0;

			if ($a['hour'] < $b['hour'])
				return -1;
			elseif ($a['hour'] > $b['hour'])
				return 1;
			elseif ($a['minute'] < $b['minute'])
				return -1;
			else
				return 1;
		});

		// Get process list
		exec("ps aux | grep 'yii-cron'", $processes, $status);

		$processes = array_filter($processes, function($process) {
			if (strpos($process, '/bin/sh -c') === false)
				return false;

			return preg_match('/(\S+)\s+.*\s+.*\s+.*\s+.*\s+.*\s+.*\s+.*\s+.*\s+.*\s+(.*)/', $process) === 1;
		});

		$processes = array_map(function($raw) {
			preg_match('/(\S+)\s+.*\s+.*\s+.*\s+.*\s+.*\s+.*\s+.*\s+.*\s+.*\s+(.*)/', $raw, $matches);

			return [
				'user' => $matches[1],
				'command' => str_replace('"', '', $matches[2]),
			];
		}, $processes);

		return $this->render('index', [
			'cronjobs' => $cronjobs,
			'processes' => $processes,
		]);
	}

	public function actionCount($command)
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		$movieDb = new MovieDb;
		$updates = null;
		$force = (strpos($command, '--force') !== false);

		if (strpos($command, 'sync/shows') !== false) {
			$model = Show::find()
				->with('language');

			if ($force) {
				$updates = $model
					->where('updated_at <= :time', [':time' => date('Y-m-d H:i:s', time() - 3600 * 24)])
					->orWhere(['updated_at' => null])
					->count();
			} else {
				$updates = $model
					->where(['updated_at' => null])
					->count();
			}
		} elseif (strpos($command, 'sync/seasons') !== false) {
			$model = Season::find()
				->with('show');

			if ($force) {
				$updates = $model
					->where('updated_at <= :time', [':time' => date('Y-m-d H:i:s', time() - 3600 * 24)])
					->orWhere(['updated_at' => null])
					->count();
			} else {
				$updates = $model
					->where(['updated_at' => null])
					->count();
			}
		} elseif (strpos($command, 'sync/episodes') !== false) {
			$model = Episode::find()
				->with('season')
				->with('season.show');

			if ($force) {
				$updates = $model
					->where('updated_at <= :time', [':time' => date('Y-m-d H:i:s', time() - 3600 * 24)])
					->orWhere(['updated_at' => null])
					->count();
			} else {
				$updates = $model
					->where(['updated_at' => null])
					->count();
			}
		} elseif (strpos($command, 'sync/movies-changes') !== false) {
			$movieChanges = $movieDb->getMovieChanges();

			$syncStatus = SyncStatus::find()
				->where([
					'name' => 'movie_changes',
					'updated' => date('Y-m-d'),
				])
				->one();

			if ($syncStatus !== null)
				$completedChanges = unserialize($syncStatus->value);
			else
				$completedChanges = [];

			$updates = Movie::find()
				->where(['themoviedb_id' => $movieChanges])
				->andWhere(['not in', 'themoviedb_id', $completedChanges])
				->count();
		} elseif (strpos($command, 'sync/movies-similar') !== false) {
			$updates = MovieSimilar::find()
				->where(['similar_to_movie_id' => null])
				->count();
		} elseif (strpos($command, 'sync/popular-shows') !== false) {
			$model = Language::find();

			if ($force) {
				$updates = $model->count();
			} else {
				$updates = $model
					->where(['popular_shows_updated_at' => null])
					->orWhere('[[popular_shows_updated_at]] <= :time')
					->addParams([
						':time' => date('Y-m-d H:i:s', time() - (3600 * 24 * 7))
					])
					->count();
			}

			$updates *= 20;
		} elseif (strpos($command, 'sync/popular-movies') !== false) {
			$model = Language::find();

			if ($force)
				$updates = $model->count();
			else
				$updates = $model
					->where(['popular_movies_updated_at' => null])
					->orWhere('[[popular_movies_updated_at]] <= :time')
					->addParams([
						':time' => date('Y-m-d H:i:s', time() - (3600 * 24 * 7))
					])
					->count();

			$updates *= 20;
		} elseif (strpos($command, 'sync/movies') !== false) {
			$model = Movie::find()
				->with('language');

			if ($force) {
				$updates = $model
					->count();
			} else {
				$updates = $model
					->where(['updated_at' => null])
					->count();
			}
		} elseif (strpos($command, 'sync/persons') !== false) {
			$model = Person::find();

			if ($force) {
				$updates = $model
					->where(['updated_at' => null])
					->orWhere('[[updated_at]] <= :time')
					->addParams([':time' => date('Y-m-d H:i:s', time() - 3600 * 24)])
					->count();
			} else {
				$personChanges = $movieDb->getPersonChanges();

				$syncStatus = SyncStatus::find()
					->where([
						'name' => 'person_changes',
						'updated' => date('Y-m-d'),
					])
					->one();

				if ($syncStatus !== null)
					$completedChanges = unserialize($syncStatus->value);
				else
					$completedChanges = [];

				$updates = $model
					->where(['id' => $personChanges])
					->orWhere(['updated_at' => null])
					->andWhere(['deleted_at' => null])
					->andWhere(['not in', 'id', $completedChanges])
					->count();
			}
		} elseif (strpos($command, 'sync/tv-changes') !== false) {
			$tvChanges = $movieDb->getTvChanges();

			$syncStatus = SyncStatus::find()
				->where([
					'name' => 'tv_changes',
					'updated' => date('Y-m-d'),
				])
				->one();

			if ($syncStatus !== null)
				$completedChanges = unserialize($syncStatus->value);
			else
				$completedChanges = [];

			$updates = Show::find()
				->where(['themoviedb_id' => $tvChanges])
				->andWhere(['not in', 'themoviedb_id', $completedChanges])
				->count();
		}

		if ($updates !== null)
			return [
				'success' => true,
				'updates' => LanguageHelper::number($updates),
			];
		else
			return [
				'success' => false,
				'updates' => 0,
				'message' => Yii::t('Update', 'The command {command} is not implemented yet!', [
					'command' => $command,
				]),
			];
	}
}
