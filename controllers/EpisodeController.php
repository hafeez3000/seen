<?php namespace app\controllers;

use \Yii;
use \yii\web\Controller;
use \yii\filters\AccessControl;

use \app\models\Episode;

class EpisodeController extends Controller
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['seen', 'unseen'],
				'rules' => [
					[
						'actions' => ['seen', 'unseen'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
	}

	public function actionSeen()
	{
		if (!Yii::$app->request->isPost || Yii::$app->request->post('id') === null)
			throw new yii\web\BadRequestHttpException;

		$episode = Episode::find()
			->where(['id' => Yii::$app->request->post('id')])
			->with('season')
			->one();
		if ($episode === null)
			throw new yii\web\NotFoundHttpException;

		if ($episode->markSeen())
			return json_encode([
				'success' => true
			]);
		else
			return json_encode([
				'success' => false,
			]);
	}

	public function actionUnseen()
	{
		if (!Yii::$app->request->isPost || Yii::$app->request->post('id') === null)
			throw new yii\web\BadRequestHttpException;

		$episode = Episode::find()
			->where(['id' => Yii::$app->request->post('id')])
			->with('season')
			->one();
		if ($episode === null)
			throw new yii\web\NotFoundHttpException;

		if ($episode->markUnseen())
			return json_encode([
				'success' => true
			]);
		else
			return json_encode([
				'success' => false,
			]);
	}
}
