<?php namespace app\modules\admin\controllers;

use \Yii;
use \yii\filters\AccessControl;
use \yii\data\Pagination;

use \app\modules\admin\controllers\BaseController;
use \app\models\Show;
use \app\models\Season;
use \app\models\Episode;
use \app\models\Movie;
use \app\models\MovieSimilar;
use \app\models\Language;
use \app\models\Person;
use \app\components\MovieDb;

class UpdateController extends BaseController
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['index'],
				'rules' => [
					[
						'actions' => ['index'],
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
		$line = exec("crontab -l", $cronjobs, $status);

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

		return $this->render('index', [
			'cronjobs' => $cronjobs,
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

			$updates = Movie::find()
				->where(['themoviedb_id' => $movieChanges])
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
			$updates = Language::find()->count() * 20;
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

				$updates = $model
					->where(['id' => $personChanges])
					->orWhere(['updated_at' => null])
					->count();
			}
		}

		if ($updates !== null)
			return [
				'success' => true,
				'updates' => $updates,
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
