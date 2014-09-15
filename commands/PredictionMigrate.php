<?php namespace app\commands;

use \Yii;
use \yii\console\Controller;

use \PredictionIO\PredictionIOClient;

class PredictionMigrateController extends Controller
{
	public function actionImport()
	{
		$client = PredictionIOClient::factory([
			'appkey' => Yii::$app->params['prediction']['key'],
		]);
	}
}
