<?php namespace app\modules\admin\controllers;

use \Yii;
use \yii\filters\AccessControl;
use \yii\data\Pagination;

use \app\modules\admin\controllers\BaseController;
use \app\models\Language;
use \app\models\search\LanguageSearch;

class LanguageController extends BaseController
{
	/**
	* @inheritdoc
	*/
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['index', 'view', 'update'],
				'rules' => [
					[
						'actions' => ['index', 'view', 'update'],
						'allow' => true,
						'roles' => ['manageLanguages'],
					],
				],
			],
		];
	}

	public function actionIndex()
	{
		$filterModel = new LanguageSearch;
		$dataProvider = $filterModel->search($_GET);

		return $this->render('admin', [
			'dataProvider' => $dataProvider,
			'filterModel' => $filterModel,
		]);
	}

	public function actionView($id)
	{
		$model = Language::findOne($id);
		if ($model === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Language', 'The language #{$id} does not exist', ['id' => $id]));

		return $this->render('view', [
			'model' => $model,
		]);
	}

	public function actionUpdate($id)
	{
		$model = Language::findOne($id);
		if ($model === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Language', 'The language #{$id} does not exist', ['id' => $id]));

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		} else {
			return $this->render('update', [
				'model' => $model,
			]);
		}
	}
}
