<?php namespace app\commands;

use \Yii;
use \yii\console\Controller;

use \PredictionIO\PredictionIOClient;

class PredictionMigrateController extends Controller
{
	public function actionImport()
	{
		$client = PredictionIOClient::factory(array("appkey" => "<your app key>"));
	}
}
