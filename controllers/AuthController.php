<?php namespace app\controllers;

use \Yii;
use \yii\filters\AccessControl;
use \yii\web\Controller;

use \app\components\MovieDb;

class AuthController extends Controller
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['themoviedb', 'themoviedbCallback'],
				'rules' => [
					[
						'actions' => ['themoviedb', 'themoviedbCallback'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
	}

	/**
	 * Link themoviedb with local account.
	 */
	public function actionThemoviedb()
	{
		$themoviedb = new MovieDb;

		$requestToken = $themoviedb->createRequestToken();
		return $this->redirect('https://www.themoviedb.org/authenticate/' . $requestToken . '?' . http_build_query([
			'redirect_to' => Yii::$app->urlManager->createAbsoluteUrl(['/auth/themoviedb/callback'])
		]));
	}

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
		if (!$user->save()) {
			Yii::$app->session->setFlash('error', Yii::t('Auth/Themoviedb', 'Error while authenticating!'));
			return $this->redirect(['/user/account']);
		}

		Yii::$app->session->setFlash('success', Yii::t('Auth/Themoviedb', 'TheMovieDB account successfully connected.'));
		return $this->redirect(['/user/account']);
	}
}
