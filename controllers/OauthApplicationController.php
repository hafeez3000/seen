<?php namespace app\controllers;

use \Yii;
use \yii\web\Controller;
use \yii\filters\AccessControl;

use \app\models\oauth\Application;

class OauthApplicationController extends Controller
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['index', 'create', 'update', 'delete'],
				'rules' => [
					[
						'actions' => ['index', 'create', 'update', 'delete'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
	}

	public function actionIndex()
	{
		$applications = Application::find()
			->where(['user_id' => Yii::$app->user->Id])
			->all();

		return $this->render('/oauth/application/index', [
			'applications' => $applications,
		]);
	}

	public function actionCreate()
	{
		return $this->render('/oauth/application/create', [
			'model' => null,
		]);
	}
}
