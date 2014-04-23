<?php namespace app\controllers;

use \Yii;
use \yii\filters\AccessControl;
use \yii\web\Controller;

class ImportController extends Controller
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['foundd'],
				'rules' => [
					[
						'actions' => ['foundd'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
	}

	public function actionFoundd()
	{
		$file = Yii::$app->basePath . '/upload/import/' . Yii::$app->user->id . '-foundd.json';

		if (!file_exists($file)) {
			Yii::$app->session->setFlash('error', Yii::t('Import/Foundd', 'The uploaded file could not be found! Please try again.'));

			return $this->redirect(['user/import']);
		}

		$json = json_decode(file_get_contents($file));

		if ($json === false) {
			Yii::$app->session->setFlash('error', Yii::t('Import/Foundd', 'The uploaded file is corrupt.'));

			return $this->redirect(['user/import']);
		}

		$json = (array) $json;

		if (!isset($json['foundd-export']) || $json['foundd-export'] != '1.0') {
			Yii::$app->session->setFlash('error', Yii::t('Import/Foundd', 'The uploaded file is invalid.'));

			return $this->redirect(['user/import']);
		}

		if (!isset($json['tvShowRatings']) || !isset($json['movieRatings'])) {
			Yii::$app->session->setFlash('error', Yii::t('Import/Foundd', 'The uploaded file does not contain movie or tv show ratings.'));

			return $this->redirect(['user/import']);
		}

		return $this->render('foundd', [
			'shows' => $json['tvShowRatings'],
			'movies' => $json['movieRatings'],
		]);
	}
}
