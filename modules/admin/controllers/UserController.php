<?php namespace app\modules\admin\controllers;

use \Yii;
use \yii\filters\AccessControl;

use \app\modules\admin\controllers\BaseController;
use \app\modules\admin\models\UserSearch;
use \app\models\User;

class UserController extends BaseController
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['index'],
				'rules' => [
					[
						'actions' => ['index'],
						'allow' => true,
						'roles' => ['viewUsers'],
					],
				],
			],
		];
	}

	public function actionIndex()
	{
		$filterModel = new UserSearch;
		$dataProvider = $filterModel->search($_GET);

		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'filterModel' => $filterModel,
		]);
	}

	public function actionView($id)
	{
		$model = User::find()
			->where(['id' => $id])
			->with([
				'language',
				'allShows',
			])
			->one();
		if ($model === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('The user #{id} could not be found!', ['id' => $id]));

		return $this->render('view', [
			'model' => $model,
		]);
	}
}
