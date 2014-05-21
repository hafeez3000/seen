<?php namespace app\modules\admin\controllers;

use \Yii;
use \yii\filters\AccessControl;
use \yii\data\Pagination;

use \app\modules\admin\controllers\BaseController;
use \app\models\Language;
use \app\models\search\LanguageSearch;

class SiteController extends BaseController
{
	public $layout = 'admin';

	public function actions()
	{
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
		];
	}

	public function actionIndex()
	{
		return $this->render('index');
	}
}
