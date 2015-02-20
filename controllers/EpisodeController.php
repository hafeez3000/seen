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

	public function actionSeen($id = null)
	{
		if (Yii::$app->request->isAjax) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			$id = Yii::$app->request->post('id');
		}

		if ($id === null)
			throw new \yii\web\BadRequestHttpException;

		$episode = Episode::find()
			->where(['id' => $id])
			->with([
				'season',
				'season.show'
			])
			->one();
		if ($episode === null)
			throw new \yii\web\NotFoundHttpException;

		if ($episode->markSeen()) {
			if (Yii::$app->request->isAjax)
				return [
					'success' => true
				];
			else
				return $this->redirect(['/tv/view', 'slug' => $episode->season->show->slug]);
		} else {
			if (Yii::$app->request->isAjax)
				return [
					'success' => false,
				];
			else
				return $this->redirect(['/tv/view', 'slug' => $episode->season->show->slug]);
		}
	}

	public function actionUnseen($id = null)
	{
		if (Yii::$app->request->isAjax) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			$id = Yii::$app->request->post('id');
		}

		if ($id === null)
			throw new \yii\web\BadRequestHttpException;

		$episode = Episode::find()
			->where(['id' => $id])
			->with([
				'season',
				'season.show'
			])
			->one();
		if ($episode === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Episode', 'The episode could not be found!'));

		if ($episode->markUnseen()) {
			if (Yii::$app->request->isAjax)
				return [
					'success' => true
				];
			else
				return $this->redirect(['/tv/view', 'slug' => $episode->season->show->slug]);
		} else {
			if (Yii::$app->request->isAjax)
				return [
					'success' => false,
				];
			else
				return $this->redirect(['/tv/view', 'slug' => $episode->season->show->slug]);
		}
	}
}
