<?php namespace app\modules\admin\controllers;

use \Yii;
use \yii\filters\AccessControl;

use \app\components\MovieDb;

use \app\models\Season;

class MissingController extends BaseController
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['index', 'syncSeasons'],
				'rules' => [
					[
						'actions' => ['index', 'syncSeasons'],
						'allow' => true,
						'roles' => ['admin'],
					],
				],
			],
		];
	}

	/**
	 * Sync a season.
	 *
	 * @param int $id Show TheMovieDB ID
	 * @param int $season
	 * @param int $offset
	 *
	 * @return void
	 */
	public function actionSyncMultiple($id, $season, $offset)
	{
		\Yii::$app->response->format = 'json';

		$movieDb = new MovieDb;

		$season = Season::find()
			->joinWith('show')
			->where([
				'{{%show}}.[[themoviedb_id]]' => $id,
				'number' => $season,
			])
			->orderBy([
				'id' => SORT_ASC,
			])
			->limit(1)
			->offset($offset)
			->one();

		if (!$movieDb->syncSeason($season)) {
			throw new \yii\web\HttpException(500, 'Could not sync season!');
		}

		return [
			'success' => true,
		];
	}

	/**
	 * Sync a season.
	 *
	 * @param int $id Season ID
	 * @param int $i JS index
	 *
	 * @return void
	 */
	public function actionSync($id, $i)
	{
		\Yii::$app->response->format = 'json';

		$movieDb = new MovieDb;

		$season = Season::findOne($id);
		$episodesBeforeSync = count($season->episodes);

		if (!$movieDb->syncSeason($season)) {
			throw new \yii\web\HttpException(500, 'Could not sync season!');
		}

		$season = Season::find()
			->where(['id' => $id])
			->with(['show', 'show.language'])
			->one();
		$episodesAfterSync = count($season->episodes);

		return [
			'success' => true,
			'name' => $season->show->name . ' (Season ' . $season->number . ' - ' . $season->show->language->iso . ')',
			'url' => Yii::$app->urlManager->createAbsoluteUrl(['tv/view', 'slug' => $season->show->slug]),
			'edit_url' => 'https://www.themoviedb.org/tv/' . $season->show->themoviedb_id . '/season/' . $season->number . '?' . http_build_query(['language' => $season->show->language->iso]),
			'added' => ($episodesAfterSync - $episodesBeforeSync),
			'i' => $i,
		];
	}

	public function actionSyncSeasons()
	{
		$seasons = Yii::$app->db->createCommand('
			SELECT DISTINCT
				{{%season}}.[[id]]
			FROM
				{{%season}}
			WHERE
				{{%season}}.[[id]] IN (
					SELECT DISTINCT
						{{%episode}}.[[season_id]]
					FROM
						{{%episode}}
					WHERE
						{{%episode}}.[[season_id]] = {{%season}}.[[id]] AND
						{{%season}}.[[number]] <> 0
					HAVING
						COUNT({{%episode}}.[[number]]) < MAX({{%episode}}.[[number]])
				)
		')->queryAll();

		return $this->render('sync-seasons', [
			'seasons' => $seasons,
		]);
	}

	/**
	 * Sync all seasons with missing episodes
	 *
	 * @return string
	 */
	public function actionIndex()
	{
		$seasons = Yii::$app->db->createCommand('
			SELECT DISTINCT
				{{%show}}.[[original_name]],
				{{%show}}.[[themoviedb_id]],
				{{%season}}.[[number]]
			FROM
				{{%show}},
				{{%season}}
			WHERE
				{{%show}}.[[id]] = {{%season}}.[[show_id]] AND
				{{%season}}.[[id]] IN (
					SELECT DISTINCT
						{{%episode}}.[[season_id]]
					FROM
						{{%episode}}
					WHERE
						{{%episode}}.[[deleted_at]] IS NULL AND
						{{%episode}}.[[season_id]] = {{%season}}.[[id]] AND
						{{%season}}.[[number]] <> 0
					HAVING
						COUNT({{%episode}}.[[number]]) < MAX({{%episode}}.[[number]])
				)
		')->queryAll();

		for ($i = 0; $i < count($seasons); $i++) {
			$command = Yii::$app->db->createCommand('
				SELECT
					COUNT({{%season}}.[[id]]) AS [[count]]
				FROM
					{{%season}},
					{{%show}}
				WHERE
					{{%show}}.[[themoviedb_id]] = :themoviedb_id AND
					{{%season}}.[[show_id]] = {{%show}}.[[id]] AND
					{{%season}}.[[number]] = :season_number
				', [
					':themoviedb_id' => $seasons[$i]['themoviedb_id'],
				]);
			$command->bindValues([
				':themoviedb_id' => $seasons[$i]['themoviedb_id'],
				':season_number' => $seasons[$i]['number'],
			]);
			$countRaw = $command->queryOne();
			$seasons[$i]['season_count'] = $countRaw['count'];
		}

		return $this->render('index', [
			'seasons' => $seasons,
		]);
	}
}
