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
				'only' => ['index', 'view', 'create', 'update', 'regenerate', 'delete'],
				'rules' => [
					[
						'actions' => ['index', 'view', 'create', 'update', 'regenerate', 'delete'],
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
		$model = new Application;
		$model->scenario = 'create';

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['oauth-application/view', 'id' => $model->id]);
		}

		return $this->render('/oauth/application/create', [
			'model' => $model,
		]);
	}

	public function actionView($id)
	{
		$model = $this->loadModel($id);

		return $this->render('/oauth/application/view', [
			'model' => $model,
			'showSecret' => (boolean) Yii::$app->request->get('showSecret', false),
		]);
	}

	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);
		$model->scenario = 'update';

		if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
			if ($model->save())
				Yii::$app->session->setFlash('success', Yii::t('Oauth/Application', 'Application successfully updated.'));

			return $this->redirect(['view', 'id' => $model->id]);
		}

		return $this->render('/oauth/application/update', [
			'model' => $model,
		]);
	}

	public function actionRegenerate($id)
	{
		$model = $this->loadModel($id);

		if (Yii::$app->request->post('regenerate', false) !== false) {
			$model->regenerate();

			if ($model->save())
				Yii::$app->session->setFlash('success', Yii::t('Oauth/Application/Regenerate', 'The key and secret were changed successfully.'));

			return $this->redirect(['view', 'id' => $model->id]);
		} else {
			return $this->render('/oauth/application/regenerate', [
				'model' => $model,
			]);
		}
	}

	public function actionDelete($id)
	{
		$model = $this->loadModel($id);

		if (Yii::$app->request->post('delete', false) !== false) {
			$model->delete();
			Yii::$app->session->setFlash('info', Yii::t('Oauth/Application', 'You successfully deleted the application.'));

			return $this->redirect(['index']);
		} else {
			return $this->render('/oauth/application/delete', [
				'model' => $model,
			]);
		}
	}

	private function loadModel($id)
	{
		$model = Application::findOne($id);
		if ($model === null || $model->user_id != Yii::$app->user->id)
			throw new \yii\web\NotFoundHttpException(Yii::t('Oauth/Application', 'The consumer application could not be found or you are not the owner of the application.'));

		return $model;
	}
}
