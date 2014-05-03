<?php namespace app\controllers;

use \Yii;
use \yii\rest\Controller;
use \yii\filters\AccessControl;
use \yii\web\Response;
use \yii\data\ActiveDataProvider;

use \app\models\oauth\Application;
use \app\models\oauth\AccessToken;
use \app\models\Language;
use \app\models\Movie;
use \app\models\UserMovie;
use \app\models\Show;
use \app\models\UserShow;
use \app\models\UserEpisode;
use \app\models\forms\AccountForm;

class ApiV1Controller extends Controller
{
	const PAGE_SIZE = 100;

	public $serializer = [
		'class' => 'yii\rest\Serializer',
		'collectionEnvelope' => 'items',
	];

	protected $user;

	protected $scopes = array();

	public function beforeAction($action)
	{
		$this->enableCsrfValidation = false;

		Yii::$app->response->format = Response::FORMAT_JSON;
		Yii::$app->id = 'api';

		return parent::beforeAction($action);
	}

	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'matchCallback' => function($rule, $action) {
							if (!isset($_SERVER['HTTP_AUTHORIZATION']))
								throw new \yii\web\HttpException(400, 'No authorization header found!');

							$accessTokenFound = preg_match('/Bearer(.*)/', $_SERVER['HTTP_AUTHORIZATION'], $accessTokenMatch);
							if ($accessTokenFound !== 1)
								throw new \yii\web\HttpException(400, 'No bearer access token found!');

							$accessToken = AccessToken::find()
								->where(['access_token' => trim($accessTokenMatch[1])])
								->andWhere('[[expires_at]] >= :now')
								->with(['user'])
								->params([
									':now' => date('Y-m-d H:i:s')
								])
								->one();

							if ($accessToken !== null) {
								$this->user = $accessToken->user;
								$this->scopes = explode(',', $accessToken->scopes);

								if ($action->id == 'movies-watch' && !in_array(Application::SCOPE_MOVIE, $this->scopes))
									throw new \yii\web\HttpException(401, 'You do not have the permission to mark the movie as seen!');

								if ($action->id == 'movies-unwatch' && !in_array(Application::SCOPE_MOVIE, $this->scopes))
									throw new \yii\web\HttpException(401, 'You do not have the permission to mark the movie as unseen!');

								if ($action->id == 'episodes-watch' && !in_array(Application::SCOPE_TV_SHOWS, $this->scopes))
									throw new \yii\web\HttpException(401, 'You do not have the permission to mark the episode as seen!');

								if ($action->id == 'shows-subscribe' && !in_array(Application::SCOPE_TV_SHOWS, $this->scopes))
									throw new \yii\web\HttpException(401, 'You do not have the permission to subscribe to the show!');

								if ($action->id == 'shows-unsubscribe' && !in_array(Application::SCOPE_TV_SHOWS, $this->scopes))
									throw new \yii\web\HttpException(401, 'You do not have the permission to unsubscribe from the show!');

								return true;
							} else {
								throw new \yii\web\HttpException(401, 'Invalid access token!');
							}
						}
					],
				],
			],
		];
	}

	/**
	 * Returns information about the current user.
	 *
	 * @return array
	 */
	public function actionUser()
	{
		return $this->user;
	}

	/**
	 * Updates the current user and returns the complete user model.
	 *
	 * @return array
	 */
	public function actionUpdateUser()
	{
		$attributes = json_decode(Yii::$app->request->rawBody);

		$user = $this->user;
		$updated = false;

		if (isset($attributes->language)) {
			$language = Language::find()
				->where(['iso' => $attributes->language])
				->one();

			if ($language !== null) {
				$updated = true;
				$user->language_id = $language->id;
			}
		}

		if (isset($attributes->timezone)) {
			$form = new AccountForm;
			$timezones = $form->timezones();

			if (isset($timezones[$attributes->timezone])) {
				$updated = true;
				$user->timezone = $attributes->timezone;
			}
		}

		if ($updated)
			$user->save();

		return $user;
	}

	/**
	 * Returns permissions for the current user.
	 *
	 * @return array
	 */
	public function actionPermissions()
	{
		return $this->scopes;
	}

	/**
	 * Returns a list of watched movies.
	 *
	 * @return array
	 */
	public function actionMovies()
	{
		return new ActiveDataProvider([
			'query' => Movie::find()
				->distinct()
				->with(['language'])
				->leftJoin(UserMovie::tableName(), UserMovie::tableName() . '.[[movie_id]] = ' . Movie::tableName() . '.[[id]]')
				->where([UserMovie::tableName() . '.[[user_id]]' => $this->user->id]),
			'pagination' => [
				'pageSize' => self::PAGE_SIZE,
			],
		]);
	}

	/**
	 * Returns a list of subscribed shows.
	 *
	 * @return array
	 */
	public function actionShows()
	{
		return new ActiveDataProvider([
			'query' => Show::find()
				->distinct()
				->with(['language', 'seasons', 'seasons.episodes'])
				->leftJoin(UserShow::tableName(), UserShow::tableName() . '.[[show_id]] = ' . Show::tableName() . '.[[id]]')
				->where([UserShow::tableName() . '.[[user_id]]' => $this->user->id]),
			'pagination' => [
				'pageSize' => self::PAGE_SIZE,
			],
		]);
	}

	/**
	 * Returns a list of watched episodes of a specific show.
	 *
	 * @param integer $id TheMovieDatabase ID
	 * @param string $iso Language
	 *
	 * @return array
	 */
	public function actionWatchedEpisodes($id, $iso)
	{
		$language = Language::find()
			->select(['id'])
			->where(['iso' => $iso])
			->asArray()
			->one();

		if (empty($language))
			throw new \yii\web\NotFoundHttpException("The language {$iso} does not exist!");

		$show = Show::find()
			->where([
				'themoviedb_id' => $id,
				'language_id' => $language['id'],
			])
			->one();

		if ($show === null)
			throw new \yii\web\NotFoundHttpException("The show {$id} does not exist!");

		return $show->getLastEpisodes(1)->all();
	}
}
