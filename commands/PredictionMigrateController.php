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
		foreach ($moviesSeen as $userMovie) {
			$client->createEvent([
				'event' => 'watch',
				'entityType' => 'user',
				'entityId' => $userMovie->user_id,
				'targetEntityType' => 'movie',
				'targetEntityId' => 'movie-' . $userMovie->movie->themoviedb_id,
				'properties' => [
					'count' => 1,
				]
			]);
		}

		echo "Importing user tv shows...\n";

		$showsSeen = UserShow::find()
			->with([
				'show',
				'user'
			])
			->all();
		foreach ($showsSeen as $userShow) {
			$client->createEvent([
				'event' => 'subscribe',
				'entityType' => 'user',
				'entityId' => $userShow->user_id,
				'targetEntityType' => 'tv',
				'targetEntityId' => 'tv-' . $userShow->show->themoviedb_id,
			]);
		}

		echo "Finished.\n";
	}
}
