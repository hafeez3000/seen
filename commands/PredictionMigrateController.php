<?php namespace app\commands;

use \Yii;
use \yii\console\Controller;

use \PredictionIO\PredictionIOClient;

use \app\models\User;
use \app\models\Movie;
use \app\models\UserMovie;
use \app\models\Show;
use \app\models\UserShow;

class PredictionMigrateController extends Controller
{
	public function actionImport()
	{
		$client = PredictionIOClient::factory([
			'appkey' => Yii::$app->params['prediction']['key'],
		]);

		$users = User::find()
			->with(['language'])
			->asArray()
			->all();

		foreach ($users as $user) {
			$response = $client->execute($client->getCommand('create_user', [
				'pio_uid' => $user['id'],
				'language' => $user['language']['en_name'],
			]));
		}

		$movieQuery = Movie::find()
			->select([
				'themoviedb_id',
				'release_date',
				'budget',
				'revenue',
				'adult',
				'vote_average',
			])
			->distinct()
			->asArray();
		$movieCount = $movieQuery->count();
		$i = 0;

		foreach ($movieQuery->batch() as $movies) {
			$currentCount = $i * 100;
			echo "Importing movies {$currentCount}/{$movieCount}\n";

			foreach ($movies as $movie) {
				$client->execute($client->getCommand('create_item', [
					'pio_iid' => 'movie-' . $movie['themoviedb_id'],
					'pio_itypes' => 'movie',
					'year' => ($movie['release_date'] != null) ? date('Y', strtotime($movie['release_date'])) : '',
					'budget' => ($movie['budget'] > 0) ? $movie['budget'] : '',
					'revenue' => ($movie['revenue'] > 0) ? $movie['revenue'] : '',
					'adult' => ($movie['adult']) ? true : false,
					'votes' => ($movie['vote_average'] > 0) ? $movie['vote_average'] : '',
				]));
			}

			$i++;
		}

		$moviesSeen = UserMovie::find()
			->with([
				'movie',
			])
			->all();
		foreach ($moviesSeen as $movie) {
			$client->identify($movie->user_id);
			$client->execute($client->getCommand('record_action_on_item',  [
				'pio_action' => 'view',
				'pio_iid' => 'movie-' . $movie->movie->themoviedb_id,
			]));
		}

		$tvQuery = Show::find()
			->select([
				'themoviedb_id',
				'first_air_date',
				'vote_average',
			])
			->distinct()
			->asArray();
		$tvCount = $tvQuery->count();
		$i = 0;

		foreach ($tvQuery->batch() as $shows) {
			$currentCount = $i * 100;
			echo "Importing shows {$currentCount}/{$tvCount}\n";

			foreach ($shows as $show) {
				$client->execute($client->getCommand('create_item', [
					'pio_iid' => 'show-' . $show['themoviedb_id'],
					'pio_itypes' => 'show',
					'year' => ($show['first_air_date'] != null) ? date('Y', strtotime($show['first_air_date'])) : '',
					'votes' => ($show['vote_average'] > 0) ? $show['vote_average'] : '',
				]));
			}

			$i++;
		}

		$showsSeen = UserShow::find()
			->with([
				'show',
				'user'
			])
			->all();
		foreach ($showsSeen as $show) {
			$client->identify($show->user->id);
			$client->execute($client->getCommand('record_action_on_item',  [
				'pio_action' => 'view',
				'pio_iid' => 'show-' . $show->show->themoviedb_id,
			]));
		}
	}
}
