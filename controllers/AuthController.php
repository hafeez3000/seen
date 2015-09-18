<?php namespace app\controllers;

use \Yii;
use \yii\filters\AccessControl;
use \yii\web\Controller;

use \app\components\MovieDb;
use \app\components\YiiMixpanel;

class AuthController extends Controller
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['themoviedb', 'themoviedbCallback', 'themoviedbSync'],
				'rules' => [
					[
						'actions' => ['themoviedb', 'themoviedbCallback', 'themoviedbSync'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
	}

	/**
	 * Get request token from themoviedb and redirect user.
	 */
	public function actionThemoviedb()
	{
		$themoviedb = new MovieDb;

		$requestToken = $themoviedb->createRequestToken();
		return $this->redirect('https://www.themoviedb.org/authenticate/' . $requestToken . '?' . http_build_query([
			'redirect_to' => Yii::$app->urlManager->createAbsoluteUrl(['/auth/themoviedb/callback'])
		]));
	}

	/**
	 * Get session and account ID for the request token.
	 */
	public function actionThemoviedbCallback()
	{
		if (Yii::$app->request->get('request_token', null) === null)
			throw new \yii\web\BadRequestHttpException('Missing request token!');
		else
			$requestToken = Yii::$app->request->get('request_token', null);

		if (Yii::$app->request->get('approved', null) === null)
			throw new \yii\web\BadRequestHttpException('Missing approved parameter!');
		else
			$approved = Yii::$app->request->get('approved', false);

		if (!$approved) {
			Yii::$app->session->setFlash('warning', Yii::t('Auth/Themoviedb', 'You did not allow access to your TheMovieDB account!'));
			return $this->redirect(['/user/account']);
		}

		$themoviedb = new MovieDb;
		$sessionId = $themoviedb->createSessionID($requestToken);
		if ($sessionId === false) {
			Yii::$app->session->setFlash('error', Yii::t('Auth/Themoviedb', 'Error while authenticating!'));
			return $this->redirect(['/user/account']);
		}

		$user = Yii::$app->user->identity;
		$user->themoviedb_session_id = $sessionId;

		$accountId = $themoviedb->getAccountId($sessionId);
		if ($accountId === false) {
			Yii::$app->session->setFlash('error', Yii::t('Auth/Themoviedb', 'Error while authenticating!'));
			return $this->redirect(['/user/account']);
		}

		$user->themoviedb_account_id = $accountId;

		if (!$user->save()) {
			Yii::$app->session->setFlash('error', Yii::t('Auth/Themoviedb', 'Error while authenticating!'));
			return $this->redirect(['/user/account']);
		}

		Yii::$app->session->setFlash('success', Yii::t('Auth/Themoviedb', 'TheMovieDB account successfully connected.'));

		YiiMixpanel::track('TheMovieDB Connect');

		return $this->redirect(['/user/account']);
	}

	/**
	 * Sync account data.
	 */
	public function actionThemoviedbSync()
	{
		$themoviedb = new MovieDb;
		$success = $themoviedb->syncUserRatings(Yii::$app->user->identity);

		YiiMixpanel::track('TheMovieDB Sync');

		if (Yii::$app->request->isAjax) {
			Yii::$app->response->format = Response::FORMAT_JSON;

			return [
				'success' => $success,
			];
		} else {
			if ($success)
				Yii::$app->session->setFlash('success', Yii::t('Auth/Themoviedb', 'Account data synced.'));
			else
				Yii::$app->session->setFlash('error', Yii::t('Auth/Themoviedb', 'Could not sync account data!'));
			return $this->redirect(['/user/account']);
		}
	}
}
