<?php namespace app\modules\admin\controllers;

use \Yii;
use \yii\filters\AccessControl;
use \yii\data\Pagination;

use \app\modules\admin\controllers\BaseController;
use \app\models\Log;

class LogController extends BaseController
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['index'],
				'rules' => [
					[
						'actions' => ['index', 'important', 'missing'],
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

	public function actionImportant()
	{
		$logs = Log::find()
			->where('STRCMP([[category]], :notfound) != 0')
			->andWhere('[[level]] <= 2')
			->orderBy([
				'log_time' => SORT_DESC,
				'id' => SORT_DESC,
			])
			->params([
				':notfound' => 'yii\web\HttpException:404',
			]);

		$pages = new Pagination([
			'totalCount' => $logs->count(),
			'defaultPageSize' => 50,
		]);

		$logs = $logs
			->offset($pages->offset)
			->limit($pages->limit)
			->all();

		return $this->render('important', [
			'pages' => $pages,
			'logs' => $logs,
		]);
	}

	public function actionMissing()
	{
		$logs = Log::find()
			->where(['category' => 'yii\web\HttpException:404'])
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

		return $this->render('missing', [
			'pages' => $pages,
			'logs' => $logs,
		]);
	}

	public function actionTruncate()
	{
		if (Yii::$app->request->post('confirm', false) !== false) {
			Yii::$app->db->createCommand('TRUNCATE {{%log}}')->execute();
			Yii::$app->session->setFlash('info', Yii::t('Log/Truncate', 'Log table truncated.'));

			return $this->redirect(['index']);
		} else {
			return $this->render('truncate');
		}
	}
}
