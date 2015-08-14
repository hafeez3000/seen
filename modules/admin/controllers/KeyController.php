<?php namespace app\modules\admin\controllers;

use \Yii;
use \yii\filters\AccessControl;

use \app\models\User;
use \app\modules\admin\models\Key;
use \app\modules\admin\models\KeySearch;

class KeyController extends BaseController
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['index'],
				'rules' => [
					[
						'actions' => ['index', 'generate'],
						'allow' => true,
						'roles' => ['admin'],
					],
				],
			],
		];
	}

	public function actionIndex()
	{
		$filterModel = new KeySearch;
		$dataProvider = $filterModel->search($_GET);

		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'filterModel' => $filterModel,
		]);
	}

	public function actionGenerate()
	{
		$key = new Key;
		$key->generate();
		if ($key->save())
			Yii::$app->session->setFlash('success', Yii::t('Key', 'Key successfully generated.'));
		else
			Yii::$app->session->setFlash('error', Yii::t('Key', 'Key could not be saved!'));

		return $this->redirect(['index']);
	}
}
