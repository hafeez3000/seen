<?php namespace app\controllers;

use \Yii;
use \yii\filters\AccessControl;
use \yii\web\Controller;
use \yii\filters\VerbFilter;

use \app\models\forms\AccountForm;

class UserController extends Controller
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['account'],
				'rules' => [
					[
						'actions' => ['account'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
	}

	public function actionAccount()
	{
		$model = new AccountForm(Yii::$app->user->identity);

		if ($model->load(Yii::$app->request->post()) && $model->save())
			Yii::$app->session->setFlash('success', Yii::t('User/Account', 'Settings saved.'));

		$model->password = '';

		return $this->render('account', [
			'model' => $model,
		]);
	}
}
