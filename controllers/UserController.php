<?php namespace app\controllers;

use \Yii;
use \yii\filters\AccessControl;
use \yii\web\Controller;

use \app\models\forms\AccountForm;
use \app\models\forms\ImportForm;

class UserController extends Controller
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['account', 'import'],
				'rules' => [
					[
						'actions' => ['account', 'import'],
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

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->session->setFlash('success', Yii::t('User/Account', 'Settings saved.'));

			return $this->redirect(['account']);
		}

		$model->password = '';

		return $this->render('account', [
			'model' => $model,
		]);
	}

	public function actionImport()
	{
		$model = new ImportForm;

		if ($model->load(Yii::$app->request->post())) {
			$model->file = \yii\web\UploadedFile::getInstance($model, 'file');

			if ($model->validate() && $model->upload()) {
				Yii::$app->session->setFlash('success', Yii::t('User/Import', 'File uploaded! Trying to find matching data...'));

				return $this->redirect(['import/' . $model->type]);
			}
		}

		return $this->render('import', [
			'model' => $model,
		]);
	}
}
