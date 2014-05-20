<?php namespace app\modules\admin\controllers;

use \Yii;
use \yii\filters\AccessControl;
use \yii\data\Pagination;

use \app\modules\admin\controllers\BaseController;
use \app\models\search\UserSearch;

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
}