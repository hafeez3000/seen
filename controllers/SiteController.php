<?php namespace app\controllers;

use \Yii;
use \yii\filters\AccessControl;
use \yii\web\Controller;
use \yii\filters\VerbFilter;

use \app\models\User;
use \app\models\Language;
use \app\models\forms\LoginForm;
use \app\models\forms\SignupForm;
use \app\models\forms\ContactForm;
use \app\models\forms\PasswordResetSendForm;
use \app\models\forms\PasswordResetForm;

class SiteController extends Controller
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['login', 'logout', 'signUp'],
				'rules' => [
					[
						'actions' => ['logout'],
						'allow' => true,
						'roles' => ['@'],
					],
					[
						'actions' => ['login', 'signup', 'reset', 'resetPassword'],
						'allow' => true,
						'roles' => ['?']
					]
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

	public function randomImage()
	{
		$data = Yii::$app->db
			->createCommand('
				SELECT
					{{%show}}.*
				FROM
					{{%show}},
					{{%language}}
				WHERE
					{{%language}}.id = {{%show}}.[[language_id]] AND
				 	{{%language}}.[[iso]] = :language
				 ORDER BY
				 	RAND()
				 LIMIT 1')
			->bindValue(':language', Yii::$app->language)
			->queryOne();

		if (isset($data['backdrop_path']))
			return '<a href="' . Yii::$app->urlManager->createUrl(['tv/view', 'slug' => $data['slug']]) . '" title="' . $data['name'] . '"><img src="https://image.tmdb.org/t/p/w780' . $data['backdrop_path'] . '" alt="' . $data['name'] . '"></a>';
		else
			return '<img data-src="holder.js/524x245/#eee:#555/text:' . Yii::$app->name . '">';
	}

	public function actionIndex()
	{
		return $this->render('index');
	}

	public function actionLogin()
	{
		$this->layout = 'login';

		$model = new LoginForm;

		if ($model->load(Yii::$app->request->post()) && $model->login()) {
			Yii::$app->session->setFlash('goal', 2);
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

	public function actionSignUp()
	{
		$this->layout = 'login';

		$model = new SignupForm;

		if ($model->load(Yii::$app->request->post()) && $model->register()) {
			Yii::$app->session->setFlash('goal', 1);
			Yii::$app->session->setFlash('success', Yii::t('User/Signup', 'Welcome to SEEN!'));
			return $this->redirect(['tv/index']);
		} else
			return $this->render('sign-up', [
				'model' => $model,
			]);
	}

	public function actionReset()
	{
		$this->layout = 'login';

		$model = new PasswordResetSendForm;

		if ($model->load(Yii::$app->request->post()) && $model->send()) {
			Yii::$app->session->setFlash('info', Yii::t('User/Reset', 'Please check your emails to reset your password!'));
			return $this->redirect(['login']);
		} else
			return $this->render('reset-send', [
				'model' => $model,
			]);
	}

	public function actionResetPassword($token)
	{
		$this->layout = 'login';

		$model = new PasswordResetForm($token);

		if (User::findByResetKey($token) === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('User/Reset', 'Your password was already reset!'));

		if ($model->load(Yii::$app->request->post()) && $model->reset()) {
			Yii::$app->session->setFlash('info', Yii::t('User/Reset', 'Password changed! You can now login with your new password.'));
			return $this->redirect(['login']);
		} else {
			return $this->render('reset', [
				'model' => $model,
			]);
		}
	}

	public function actionContact()
	{
		$model = new ContactForm;
		if ($model->load(Yii::$app->request->post()) && $model->contact()) {
			Yii::$app->session->setFlash('goal', 3);
			Yii::$app->session->setFlash('success', Yii::t('Site/Contact', 'Thanks for your message! We will answer your request as soon as possible.'));

			return $this->redirect(['/site/contact']);
		} else {
			return $this->render('contact', [
				'model' => $model,
			]);
		}
	}

	public function actionImprint()
	{
		$path = $this->viewPath . '/imprint/' . Yii::$app->language . '.php';

		if (file_exists($path))
			return $this->render('imprint/' . Yii::$app->language);
		else
			return $this->render('imprint/en');
	}

	public function actionPrivacy()
	{
		$path = $this->viewPath . '/privacy/' . Yii::$app->language . '.php';

		if (file_exists($path))
			return $this->render('privacy/' . Yii::$app->language);
		else
			return $this->render('privacy/en');
	}

	public function actionLanguage($iso)
	{
		$language = Language::find()
			->select(['id', 'iso'])
			->where(['iso' => $iso])
			->asArray()
			->one();

		if ($language === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Site', 'The language does not exist!'));

		if (Yii::$app->user->isGuest) {
			Yii::$app->session->set('language', $language['iso']);
		} else {
			$user = Yii::$app->user->identity;
			$user->language_id = $language['id'];

			if (!$user->save())
				Yii::error("Could not save language #{$language['id']} for user #{$user->id}!");
		}

		if (Yii::$app->request->referrer !== null && strpos(Yii::$app->request->referrer, 'language') === false)
			return $this->redirect(Yii::$app->request->referrer);
		else
			return $this->goHome();
	}
}
