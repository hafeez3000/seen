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
use \app\components\YiiMixpanel;

class SiteController extends Controller
{
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
			return '<img data-src="holder.js/524x245?theme=gray&text=' . Yii::$app->name . '">';
	}

	public function actionIndex()
	{
		YiiMixpanel::track('Show Frontpage');

		return $this->render('index');
	}

	public function actionLogin()
	{
		if (!Yii::$app->user->isGuest)
			return $this->goHome();

		$this->layout = 'login';

		$model = new LoginForm;

		if ($model->load(Yii::$app->request->post()) && $model->login()) {
			YiiMixpanel::track('Successfull Login');

			Yii::$app->session->setFlash('success', Yii::t('Site/Login', 'Welcome back!'));

			return $this->goBack();
		} else {
			YiiMixpanel::track('Show Login');

			return $this->render('login', [
				'model' => $model,
			]);
		}
	}

	public function actionLogout()
	{
		if (Yii::$app->user->isGuest)
			return $this->goHome();

		YiiMixpanel::track('Logout');
		Yii::$app->user->logout();

		return $this->goHome();
	}

	public function actionSignUp()
	{
		if (!Yii::$app->user->isGuest)
			return $this->goHome();

		$this->layout = 'login';

		$model = new SignupForm;

		if ($model->load(Yii::$app->request->post()) && $model->register()) {
			YiiMixpanel::track('Successfull Sign Up');

			Yii::$app->session->setFlash('success', Yii::t('User/Signup', 'Welcome to SEEN! <a href="{url-account}">Update your timezone</a> or add <a href="{url-movies}">movies</a> or <a href="{url-tv}">tv shows</a>', [
				'url-account' => Url::toRoute(['/user/account']),
				'url-movies' => Url::toRoute(['/movies']),
				'url-tv' => Url::toRoute(['/tv']),
			]));

			return $this->redirect(['tv/index']);
		} else
			YiiMixpanel::track('Show Sign Up');

			return $this->render('sign-up', [
				'model' => $model,
			]);
	}

	public function actionReset()
	{
		if (!Yii::$app->user->isGuest)
			return $this->goHome();

		$this->layout = 'login';

		$model = new PasswordResetSendForm;

		if ($model->load(Yii::$app->request->post()) && $model->send()) {
			YiiMixpanel::track('Reset Password Send');

			Yii::$app->session->setFlash('info', Yii::t('User/Reset', 'Please check your emails to reset your password!'));
			return $this->redirect(['login']);
		} else {
			YiiMixpanel::track('Show Reset Password');

			return $this->render('reset-send', [
				'model' => $model,
			]);
		}
	}

	public function actionResetPassword($token)
	{
		if (!Yii::$app->user->isGuest)
			return $this->goHome();

		$this->layout = 'login';

		$model = new PasswordResetForm($token);

		if (User::findByResetKey($token) === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('User/Reset', 'Your password was already reset!'));

		if ($model->load(Yii::$app->request->post()) && $model->reset()) {
			YiiMixpanel::track('Changed Password (After Reset)');

			Yii::$app->session->setFlash('info', Yii::t('User/Reset', 'Password changed! You can now login with your new password.'));
			return $this->redirect(['login']);
		} else {
			YiiMixpanel::track('Show Change Password (After Reset)');

			return $this->render('reset', [
				'model' => $model,
			]);
		}
	}

	public function actionContact()
	{
		$model = new ContactForm;
		if ($model->load(Yii::$app->request->post()) && $model->contact()) {
			YiiMixpanel::track('Created Contact Message');
			Yii::$app->session->setFlash('success', Yii::t('Site/Contact', 'Thanks for your message! We will answer your request as soon as possible.'));

			return $this->redirect(['/site/contact']);
		} else {
			YiiMixpanel::track('Show Contact Form');

			return $this->render('contact', [
				'model' => $model,
			]);
		}
	}

	public function actionImprint()
	{
		$path = $this->viewPath . '/imprint/' . Yii::$app->language . '.php';

		YiiMixpanel::track('Show Imprint');

		if (file_exists($path))
			return $this->render('imprint/' . Yii::$app->language);
		else
			return $this->render('imprint/en');
	}

	public function actionPrivacy()
	{
		$path = $this->viewPath . '/privacy/' . Yii::$app->language . '.php';

		YiiMixpanel::track('Show Privacy');

		if (file_exists($path))
			return $this->render('privacy/' . Yii::$app->language);
		else
			return $this->render('privacy/en');
	}

	public function actionLanguage($iso)
	{
		$oldLanguage = Language::find()
			->select(['id', 'iso', 'name'])
			->where(['iso' => Yii::$app->language])
			->asArray()
			->one();

		$language = Language::find()
			->select(['id', 'iso', 'name'])
			->where(['iso' => $iso])
			->asArray()
			->one();

		if ($language === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Site', 'The language does not exist!'));

		YiiMixpanel::track('Changed Language', [
			'old_language' => $oldLanguage['name'],
			'new_language' => $language['name'],
		]);

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
			'google',
		];

		if (!in_array($service, $services)) {
			throw new \yii\web\BadRequestHttpException(Yii::t('Site/Login', 'Unknown service: {service}', [
				'service' => $service,
			]));
		}

		switch ($service) {
			case 'facebook':
				$provider = new \League\OAuth2\Client\Provider\Facebook([
					'clientId' => Yii::$app->params['oauth']['facebook']['key'],
					'clientSecret' => Yii::$app->params['oauth']['facebook']['secret'],
					'redirectUri' => Yii::$app->params['baseUrl'] . '/login/facebook',
					'graphApiVersion' => 'v2.5',
				]);

				if (Yii::$app->request->get('code', null) === null) {
					$url = $provider->getAuthorizationUrl([
						'scope' => ['email'],
					]);
					Yii::$app->session['oauth2state'] = $provider->getState();

					return $this->redirect($url);
				}

				if (Yii::$app->request->get('state', null) === null || Yii::$app->request->get('state') != Yii::$app->session['oauth2state']) {
					unset(Yii::$app->session['oauth2state']);
					throw new \yii\web\BadRequestHttpException(Yii::t('Site/Login', 'Invalid state!'));
				}

				// Get user
				$token = $provider->getAccessToken('authorization_code', [
					'code' => Yii::$app->request->get('code', '')
				]);

				$profile = $provider->getResourceOwner($token);

				// Check if user is already in database
				$user = User::findByEmail($profile->getEmail());
				if ($user === null) {
					// Create new user
					$language = Language::find()
						->where(['iso' => substr($profile->getLocale(), 0, 2)])
						->orWhere(['iso' => Yii::$app->params['lang']['default_iso']])
						->one();

					$user = new User;
					$user->email = $profile->getEmail();
					$user->name = $profile->getName();
					$user->password = 0;

					// Wait for https://github.com/thephpleague/oauth2-facebook/pull/15
					$user->timezone = 'UTC';

					if ($language !== null)
						$user->language_id = $language->id;

					if (!$user->save()) {
						Yii::error('Could not save user: ' . serialize($user->errors));
						Yii::$app->session->setFlash('error', Yii::t('User/Signup', 'Your account could not be created! Maybe your email address has the wrong format? Please register with your email address and password.'));
						return $this->redirect(['site/sign-up']);
					}

					YiiMixpanel::track('Authorize Facebook', [
						'new' => true,
					]);

					Yii::$app->user->login($user, 3600 * 24 * 30);
					Yii::$app->session->setFlash('success', Yii::t('User/Signup', 'Welcome to SEEN! <a href="{url-account}">Update your timezone</a> or add <a href="{url-movies}">movies</a> or <a href="{url-tv}">tv shows</a>', [
						'url-account' => Url::toRoute(['/user/account']),
						'url-movies' => Url::toRoute(['/movies']),
						'url-tv' => Url::toRoute(['/tv']),
					]));
					return $this->redirect(['tv/index']);
				} else {
					YiiMixpanel::track('Authorize Facebook', [
						'new' => false,
					]);
				}

				Yii::$app->user->login($user, 3600 * 24 * 30);
				Yii::$app->session->setFlash('success', Yii::t('Site/Login', 'Welcome back!'));

				return $this->goBack();

				break;
			case 'google':
				$provider = new \League\OAuth2\Client\Provider\Google([
					'clientId' => Yii::$app->params['oauth']['google']['key'],
					'clientSecret' => Yii::$app->params['oauth']['google']['secret'],
					'redirectUri' => Yii::$app->params['baseUrl'] . '/login/google',
				]);

				if (Yii::$app->request->get('error', null) !== null)
					throw new \yii\web\BadRequestHttpException(Yii::$app->request->get('error', null));

				if (Yii::$app->request->get('code', null) === null) {
					$url = $provider->getAuthorizationUrl([
						'scope' => ['email'],
					]);
					Yii::$app->session['oauth2state'] = $provider->getState();

					return $this->redirect($url);
				}

				if (Yii::$app->request->get('state', null) === null || Yii::$app->request->get('state') != Yii::$app->session['oauth2state']) {
					unset(Yii::$app->session['oauth2state']);
					throw new \yii\web\BadRequestHttpException(Yii::t('Site/Login', 'Invalid state!'));
				}

				// Get user
				$token = $provider->getAccessToken('authorization_code', [
					'code' => Yii::$app->request->get('code', '')
				]);

				$profile = $provider->getResourceOwner($token);

				// Check if user is already in database
				$user = User::findByEmail($profile->getEmail());
				if ($user === null) {
					// Create new user
					$language = Language::find()
						->where(['iso' => 'en'])
						->one();

					$user = new User;
					$user->email = $profile->getEmail();
					$user->name = $profile->getName();
					$user->password = 0;

					// Wait for https://github.com/thephpleague/oauth2-facebook/pull/15
					$user->timezone = 'UTC';

					if ($language !== null)
						$user->language_id = $language->id;

					if (!$user->save())
						throw new \yii\web\HttpException(500);

					YiiMixpanel::track('Authorize Facebook', [
						'new' => true,
					]);

					Yii::$app->user->login($user, 3600 * 24 * 30);
					Yii::$app->session->setFlash('success', Yii::t('User/Signup', 'Welcome to SEEN! <a href="{url-account}">Update your timezone</a> or add <a href="{url-movies}">movies</a> or <a href="{url-tv}">tv shows</a>', [
						'url-account' => Url::toRoute(['/user/account']),
						'url-movies' => Url::toRoute(['/movies']),
						'url-tv' => Url::toRoute(['/tv']),
					]));
					return $this->redirect(['tv/index']);
				} else {
					YiiMixpanel::track('Authorize Facebook', [
						'new' => false,
					]);
				}

				Yii::$app->user->login($user, 3600 * 24 * 30);
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
