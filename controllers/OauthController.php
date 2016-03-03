<?php namespace app\controllers;

use \Yii;
use \yii\web\Controller;
use \yii\filters\AccessControl;
use \yii\web\Response;

use \app\models\oauth\Application;
use \app\models\oauth\RequestToken;
use \app\models\oauth\AccessToken;
use \app\models\oauth\RefreshToken;

class OauthController extends Controller
{
	public function beforeAction($action)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		if ($action->id == 'access-token')
			$this->enableCsrfValidation = false;

		return parent::beforeAction($action);
	}

	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['authorize'],
				'rules' => [
					[
						'actions' => ['authorize'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
	}

	public function actionAuthorize()
	{
		Yii::$app->response->format = Response::FORMAT_HTML;

		if (Yii::$app->request->isGet) {
			$type = Yii::$app->request->get('type');
			$key = Yii::$app->request->get('client_id');
			$redirectUri = Yii::$app->request->get('redirect_uri');
			$responseType = Yii::$app->request->get('response_type');
			$scope = Yii::$app->request->get('scope');
			$state = Yii::$app->request->get('state');

			if ($key === false)
				throw new yii\web\BadRequestHttpException('Missing client_id parameter!');

			if ($type != 'web_server')
				throw new yii\web\BadRequestHttpException('Only "web_server" supported for the type parameter!');

			$application = Application::find()
				->where(['key' => $key])
				->one();

			if ($application === null)
				throw new yii\web\UnauthorizedHttpException('Invalid client_id!');

			if (empty($redirectUri))
				$redirectUri = $application->callback;

			// Store variables in session to prevent user from changing data
			Yii::$app->session->set('oauth_type', $type);
			Yii::$app->session->set('oauth_client_id', $key);
			Yii::$app->session->set('oauth_redirect_uri', $redirectUri);
			Yii::$app->session->set('oauth_response_type', $responseType);
			Yii::$app->session->set('oauth_scope', $scope);
			Yii::$app->session->set('oauth_state', $state);

			// Get valid scopes from request
			if (empty($scope))
				$scope = Application::SCOPE_READONLY;
			$scopes = explode(',', $scope);
			$validScopes = Application::scopes();

			$scopes = array_filter($scopes, function($scope) use($validScopes) {
				return isset($validScopes[$scope]);
			});

			// Check if user alreay authenticated the application with the same scopes
			$accessToken = AccessToken::find()
				->where([
					'user_id' => Yii::$app->user->id,
					'oauth_application_id' => $application->id,
					'scopes' => implode(',', $scopes),
				])
				->one();
			if ($accessToken !== null) {
				// User alreay authenticated the application
				$token = new RequestToken;
				$token->user_id = Yii::$app->user->id;
				$token->oauth_application_id = $application->id;
				$token->scopes = implode(',', $scopes);
				$token->expires_at = date('Y-m-d H:i:s', time() + 3600);

				if (!$token->save()) {
					Yii::error('Request token could not be saved: ' . serialize($token->errors));
					throw new \yii\web\HttpException(Yii::t('Oauth', 'Could not create token! Please try again later.'));
				}

				return $this->redirect($redirectUri . '?' . http_build_query([
					'success' => 'true',
					'code' => $token->request_token,
					'state' => $state,
				]));
			}

			$scopes = array_map(function($scope) use($validScopes) {
				return $validScopes[$scope];
			}, $scopes);

			return $this->render('authorize', [
				'application' => $application,
				'scopes' => $scopes,
			]);
		} elseif (Yii::$app->request->isPost) {
			$type = Yii::$app->session->get('oauth_type');
			$key = Yii::$app->session->get('oauth_client_id');
			$redirectUri = Yii::$app->session->get('oauth_redirect_uri');
			$responseType = Yii::$app->session->get('oauth_response_type');
			$scope = Yii::$app->session->get('oauth_scope');
			$state = Yii::$app->session->get('oauth_state');

			if ($key === false)
				throw new \yii\web\BadRequestHttpException('Missing client_id parameter!');

			$application = Application::find()
				->where(['key' => $key])
				->one();

			if ($application === null)
				throw new \yii\web\UnauthorizedHttpException('Invalid client_id!');

			if (Yii::$app->request->post('deny')) {
				return $this->redirect($redirectUri . http_build_query([
					'success' => 'false',
					'state' => $state,
				]));
			}

			if (empty($scope))
				$scope = Application::SCOPE_READONLY;
			$scopes = explode(',', $scope);
			$validScopes = Application::scopes();

			$scopes = array_filter($scopes, function($scope) use($validScopes) {
				return isset($validScopes[$scope]);
			});

			$token = new RequestToken;
			$token->user_id = Yii::$app->user->id;
			$token->oauth_application_id = $application->id;
			$token->scopes = implode(',', $scopes);
			$token->expires_at = date('Y-m-d H:i:s', time() + 3600);

			if (!$token->save()) {
				Yii::error('Request token could not be saved: ' . serialize($token->errors));
				throw new \yii\web\HttpException(Yii::t('Oauth', 'Could not create token! Please try again later.'));
			}

			return $this->redirect($redirectUri . '?' . http_build_query([
				'success' => 'true',
				'code' => $token->request_token,
				'state' => $state,
			]));
		} else {
			throw new \yii\web\MethodNotAllowedHttpException;
		}
	}

	public function actionAccessToken()
	{
		$requestToken = Yii::$app->request->post('code', false);
		$key = Yii::$app->request->post('client_id', false);
		$secret = Yii::$app->request->post('client_secret', false);
		$grantType = Yii::$app->request->post('grant_type', false);

		if ($requestToken === false)
			throw new \yii\web\BadRequestHttpException('Missing code parameter!');

		if ($key === false)
			throw new \yii\web\BadRequestHttpException('Missing client_id parameter!');

		if ($secret === false)
			throw new \yii\web\BadRequestHttpException('Missing client_secret parameter!');

		if ($grantType != 'authorization_code') {
			throw new \yii\web\BadRequestHttpException('Grant type has to be "authorization_code"!');
		}

		$application = Application::find()
			->where([
				'key' => $key,
				'secret' => $secret,
			])
			->one();

		if ($application === null)
			throw new \yii\web\UnauthorizedHttpException('Invalid client id or client secret!');

		$token = RequestToken::find()
			->where(['request_token' => $requestToken])
			->one();
		if ($token === null)
			throw new \yii\web\UnauthorizedHttpException("Invalid request token {$requestToken}!");

		// Update timestamp
		$token->save();

		$accessToken = new AccessToken;
		$accessToken->user_id = $token->user_id;
		$accessToken->oauth_application_id = $token->oauth_application_id;
		$accessToken->scopes = $token->scopes;
		$accessToken->expires_at = date('Y-m-d H:i:s', time() + AccessToken::EXPIRES_IN);

		if (!$accessToken->save()) {
			Yii::error('Access token could not be saved: ' . serialize($token->errors));
			throw new \yii\web\HttpException(Yii::t('Oauth', 'Could not create access token! Please try again later.'));
		}

		$refreshToken = new RefreshToken;
		$refreshToken->user_id = $token->user_id;
		$refreshToken->oauth_application_id = $token->oauth_application_id;
		$refreshToken->scopes = $token->scopes;

		if (!$refreshToken->save()) {
			Yii::error('Access token could not be saved: ' . serialize($token->errors));
			throw new \yii\web\HttpException(Yii::t('Oauth', 'Could not create access token! Please try again later.'));
		}

		return [
			'access_token' => $accessToken->access_token,
			'refresh_token' => $refreshToken->refresh_token,
			'expires_in' => AccessToken::EXPIRES_IN,
			'token_type' => "Bearer",
		];
	}
}
