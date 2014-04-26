<?php namespace app\controllers;

use \Yii;
use \yii\web\Controller;
use \yii\filters\AccessControl;
use \yii\web\Response;

use \app\models\Episode;

class EpisodeController extends Controller
{
	public function beforeAction($action)
	{
		if (Yii::$app->request->isAjax)
			$this->enableCsrfValidation = false;

		return parent::beforeAction($action);
	}

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
		Yii::$app->response->format = Response::FORMAT_JSON;

		if (!Yii::$app->request->isPost || Yii::$app->request->post('id') === null)
			throw new yii\web\BadRequestHttpException;

		$episode = Episode::find()
			->where(['id' => Yii::$app->request->post('id')])
			->with('season')
			->one();
		if ($episode === null)
			throw new yii\web\NotFoundHttpException;

		if ($episode->markSeen())
			return [
				'success' => true
			];
		else
			return [
				'success' => false,
			];
	}

	public function actionUnseen()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		if (!Yii::$app->request->isPost || Yii::$app->request->post('id') === null)
			throw new yii\web\BadRequestHttpException;

		$episode = Episode::find()
			->where(['id' => Yii::$app->request->post('id')])
			->with('season')
			->one();
		if ($episode === null)
			throw new yii\web\NotFoundHttpException;

		if ($episode->markUnseen())
			return [
				'success' => true
			];
		else
			return [
				'success' => false,
			];
	}
}
