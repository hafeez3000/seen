<?php namespace app\commands;

use \Yii;
use \yii\console\Controller;

use \predictionio\EventClient;

use \app\models\User;
use \app\models\Movie;
use \app\models\UserMovie;
use \app\models\Show;
use \app\models\UserShow;

class PredictionMigrateController extends Controller
{
	public function actionImport()
	{
		$client = new EventClient(Yii::$app->params['prediction']['key'], Yii::$app->params['prediction']['eventserver']);

		echo "Importing user movies...\n";

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

		echo "Importing user tv shows...\n";

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

		echo "Finished.\n";
	}
}
