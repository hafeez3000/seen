<?php namespace app\controllers;

use \Yii;
use \yii\web\Controller;

use \app\models\Episode;

class EpisodeController extends Controller
{
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
