<?php namespace app\controllers;

use \Yii;
use \yii\filters\AccessControl;
use \yii\web\Controller;
use \yii\helpers\Url;

use \OAuth\ServiceFactory;
use \OAuth\OAuth2\Service\Facebook;
use \OAuth\Common\Storage\Redis;
use \OAuth\Common\Consumer\Credentials;

use \Predis\Client as Predis;

use \app\models\User;
use \app\models\Language;
use \app\models\Show;
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
		$popularShows = Show::find()
			->select('{{%show}}.[[id]]')
			->from([
				'{{%show}}',
				'{{%language}}',
			])
			->where('{{%language}}.id = {{%show}}.[[language_id]]')
			->andWhere('{{%language}}.[[iso]] = :language')
			->andWhere('{{%show}}.[[backdrop_path]] != ""')
			->params([
					':language' => Yii::$app->language,
			])
			->orderBy(['{{%show}}.[[popularity]]' => SORT_DESC])
			->limit(100)
			->asArray()
			->all();

		shuffle($popularShows);

		$data = Yii::$app->db
			->createCommand('
				SELECT
					{{%show}}.[[backdrop_path]],
					{{%show}}.[[slug]],
					{{%show}}.[[name]]
				FROM
					{{%show}}
				WHERE
					{{%show}}.[[id]] = :show
				LIMIT 1', [
				':show' => array_shift($popularShows)['id'],
			])
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
			Yii::$app->session->setFlash('success', Yii::t('Site/Login', 'Welcome back!'));

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
			Yii::$app->session->setFlash('success', Yii::t('User/Signup', 'Welcome to SEEN! <a href="{url-account}">Update your timezone</a> or add <a href="{url-movies}">movies</a> or <a href="{url-tv}">tv shows</a>', [
				'url-account' => Url::toRoute(['/user/account']),
				'url-movies' => Url::toRoute(['/movies']),
				'url-tv' => Url::toRoute(['/tv']),
			]));

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
		} else {
			return $this->render('reset-send', [
				'model' => $model,
			]);
		}
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

			if (!$user->save()) {
				Yii::error("Could not save language #{$language['id']} for user #{$user->id}!");
			}
		}

		if (Yii::$app->request->referrer !== null && strpos(Yii::$app->request->referrer, 'language') === false)
			return $this->redirect(Yii::$app->request->referrer);
		else
			return $this->goHome();
	}

	public function actionOauth($service)
	{
		$services = [
			'facebook',
		];

		if (!in_array($service, $services)) {
			throw new \yii\web\BadRequestHttpException(Yii::t('Site/Login', 'Unknown service: {service}', [
				'service' => $service,
			]));
		}

		$redis = new Predis([
			'host' => Yii::$app->params['redis']['host'],
			'port' => Yii::$app->params['redis']['port'],
		]);
		$storage = new Redis($redis, 'seen_oauth_token', 'seen_oauth_state');

		$serviceFactory = new ServiceFactory;

		switch ($service) {
			case 'facebook':
				$service = $serviceFactory->createService('facebook', new Credentials(
					Yii::$app->params['oauth']['facebook']['key'],
					Yii::$app->params['oauth']['facebook']['secret'],
					Yii::$app->request->absoluteUrl
				), $storage, [Facebook::SCOPE_EMAIL]);

				// Redirect user to facebook
				if (Yii::$app->request->get('code', null) === null) {
					return $this->redirect($service->getAuthorizationUri()->getAbsoluteUri());
				}

				// Get access token
				$service->requestAccessToken(Yii::$app->request->get('code', ''));

				$profile = json_decode($service->request('/me'));

				if (!isset($profile->email))
					throw new \yii\web\BadRequestHttpException(Yii::t('Site/Login', 'SEEN needs access to your email to log you in via facebook!'));

				$user = User::findByEmail($profile->email);
				if ($user === null) {
					$language = Language::find()
						->where(['iso' => substr($profile->locale, 0, 2)])
						->one();

					$user = new User;
					$user->email = $profile->email;
					$user->name = $profile->name;
					$user->password = 0;

					try {
						$timezone = timezone_name_from_abbr('', ($profile->timezone - 1) * 3600, 0);
					} catch (\Exception $e) {
						$timezone = 'UTC';
					}
					if ($timezone === false)
						$timezone = 'UTC';
					$user->timezone = $timezone;

					if ($language !== null) {
						$user->language_id = $language->id;
					}

					if (!$user->save()) {
						throw new \yii\web\HttpException(500);
					}

					Yii::$app->user->login($user, 3600 * 24 * 30);
					Yii::$app->session->setFlash('goal', 1);
					Yii::$app->session->setFlash('success', Yii::t('User/Signup', 'Welcome to SEEN! <a href="{url-account}">Update your timezone</a> or add <a href="{url-movies}">movies</a> or <a href="{url-tv}">tv shows</a>', [
						'url-account' => Url::toRoute(['/user/account']),
						'url-movies' => Url::toRoute(['/movies']),
						'url-tv' => Url::toRoute(['/tv']),
					]));
					return $this->redirect(['tv/index']);
				}

				Yii::$app->user->login($user, 3600 * 24 * 30);
				Yii::$app->session->setFlash('goal', 2);
				Yii::$app->session->setFlash('success', Yii::t('Site/Login', 'Welcome back!'));
				return $this->goBack();

				break;
			default:
				throw new \yii\web\BadRequestHttpException(Yii::t('Site/Login', 'Unknown service: {service}', [
					'service' => $service,
				]));
		}
	}
}
