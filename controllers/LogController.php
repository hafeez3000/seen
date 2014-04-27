<?php namespace app\controllers;

use \Yii;
use \yii\web\Controller;
use \yii\filters\AccessControl;
use \yii\data\Pagination;

use \app\models\Log;

class LogController extends Controller
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
						'roles' => ['viewLogs'],
					],
				],
			],
		];
	}

	public function actionIndex()
	{
		$logs = Log::find()
			->orderBy([
				'log_time' => SORT_DESC,
				'id' => SORT_DESC,
			]);

		$pages = new Pagination([
			'totalCount' => $logs->count(),
			'defaultPageSize' => 50,
		]);

		$logs = $logs
			->offset($pages->offset)
			->limit($pages->limit)
			->all();

		return $this->render('index', [
			'pages' => $pages,
			'logs' => $logs,
		]);
	}
}
