<?php namespace app\controllers;

use \Yii;
use \yii\filters\AccessControl;
use \yii\web\Controller;
use \yii\filters\VerbFilter;

use \app\models\forms\LoginForm;
use \app\models\forms\ContactForm;

class SiteController extends Controller
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['login', 'logout', 'signup'],
				'rules' => [
					[
						'actions' => ['logout'],
						'allow' => true,
						'roles' => ['@'],
					],
					[
						'actions' => ['login', 'signup'],
						'allow' => true,
						'roles' => ['?']
					]
				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'logout' => ['post'],
				],
			],
		];
	}

	public function actions()
	{
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
			'captcha' => [
				'class' => 'yii\captcha\CaptchaAction',
				'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
			],
		];
	}

	public function actionIndex()
	{
		return $this->render('index');
	}

	public function actionLogin()
	{
		$model = new LoginForm();
		if ($model->load(Yii::$app->request->post()) && $model->login()) {
			return $this->goBack();
		} else {
			return $this->render('login', [
				'model' => $model,
			]);
		}
	}

	public function actionLogout()
	{
		Yii::$app->user->logout();

		return $this->goHome();
	}

	public function actionContact()
	{
		$model = new ContactForm();
		if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
			Yii::$app->session->setFlash('contactFormSubmitted');

			return $this->refresh();
		} else {
			return $this->render('contact', [
				'model' => $model,
			]);
		}
	}

	public function actionAbout()
	{
		return $this->render('about');
	}

	public function actionImprint()
	{
		$path = $this->viewPath . '/imprint/' . Yii::$app->language . '.php';

		/*if (file_exists($path))
			$this->render('imprint/' . Yii::$app->language);
		else*/
			$this->render('imprint/en');
	}

	public function actionPrivacy()
	{

	}
}
