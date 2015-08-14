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
use \app\models\UserShowRun;
use \app\models\Episode;
use \app\models\forms\AccountForm;
use \app\components\MovieDb;

class ApiV1Controller extends Controller
{
	const PAGE_SIZE = 100;

	public $serializer = [
		'class' => 'yii\rest\Serializer',
		'collectionEnvelope' => 'items',
	];

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
								Yii::$app->user->setIdentity($accessToken->user);
								$this->scopes = explode(',', $accessToken->scopes);

								if ($action->id == 'update-user' && !in_array(Application::SCOPE_ACCOUNT, $this->scopes))
									throw new \yii\web\HttpException(401, 'You do not have the permission to update the user account!');

								if ($action->id == 'movie-watch' && !in_array(Application::SCOPE_MOVIES, $this->scopes))
									throw new \yii\web\HttpException(401, 'You do not have the permission to label the movie as seen!');

								if ($action->id == 'movie-unwatch' && !in_array(Application::SCOPE_MOVIES, $this->scopes))
									throw new \yii\web\HttpException(401, 'You do not have the permission to label the movie as unseen!');

								if ($action->id == 'show-subscribe' && !in_array(Application::SCOPE_TV_SHOWS, $this->scopes))
									throw new \yii\web\HttpException(401, 'You do not have the permission to subscribe to the show!');

								if ($action->id == 'show-unsubscribe' && !in_array(Application::SCOPE_TV_SHOWS, $this->scopes))
									throw new \yii\web\HttpException(401, 'You do not have the permission to unsubscribe from the show!');

								if ($action->id == 'episode-watch' && !in_array(Application::SCOPE_TV_SHOWS, $this->scopes))
									throw new \yii\web\HttpException(401, 'You do not have the permission to label the episode as seen!');

								if ($action->id == 'episode-unwatch' && !in_array(Application::SCOPE_TV_SHOWS, $this->scopes))
									throw new \yii\web\HttpException(401, 'You do not have the permission to label the episode as unseen!');

								// Update timestamp
								$accessToken->save();

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
	 * @return User
	 */
	public function actionUser()
	{
		return Yii::$app->user->identity;
	}

	/**
	 * Updates the current user and returns the complete user model.
	 *
	 * @return User
	 */
	public function actionUpdateUser()
	{
		$attributes = json_decode(Yii::$app->request->rawBody);

		$user = Yii::$app->user->identity;
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

		if ($updated) {
			if (!$user->save()) {
				Yii::error("Could not update user #{%user->id}: " . serialize($user->errors), 'application\api\v1');
			}
		}

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
	 * @return ActiveDataProvider
	 */
	public function actionMovies()
	{
		return new ActiveDataProvider([
			'query' => Movie::find()
				->distinct()
				->with(['language', 'userWatches'])
				->leftJoin(UserMovie::tableName(), UserMovie::tableName() . '.[[movie_id]] = ' . Movie::tableName() . '.[[id]]')
				->where([UserMovie::tableName() . '.[[user_id]]' => Yii::$app->user->id]),
			'pagination' => [
				'pageSize' => self::PAGE_SIZE,
			],
		]);
	}

	/**
	 * Label movie as seen.
	 *
	 * @param integer $id
	 * @param string $iso
	 *
	 * @return Movie
	 */
	public function actionMovieWatch($id, $iso)
	{
		$language = Language::find()
			->select(['id'])
			->where(['iso' => $iso])
			->asArray()
			->one();
		if (empty($language))
			throw new \yii\web\NotFoundHttpException("The language {$iso} does not exist!");

		$movie = Movie::find()
			->where([
				'themoviedb_id' => $id,
				'language_id' => $language['id'],
			])
			->one();
		if ($movie === null) {
			$themoviedb = new MovieDb;
			$movie = new Movie;
			$movie->themoviedb_id = $id;
			$movie->language_id = $language['id'];

			$found = $themoviedb->syncMovie($movie);

			if ($found === false)
				throw new \yii\web\NotFoundHttpException("The movie #{$id} does not exist!");
		}

		$watched = new UserMovie;
		$watched->user_id = Yii::$app->user->id;
		$watched->movie_id = $movie->id;

		if (!$watched->save()) {
			Yii::error("Could not save watched movie #{$watched->movie_id} for user #{$watched->user_id}: " . serialize($watched->errors), 'application\api\v1');
		}

		return $movie;
	}

	/**
	 * Returns a list of subscribed shows.
	 *
	 * @return ActiveDataProvider
	 */
	public function actionShows()
	{
		return new ActiveDataProvider([
			'query' => Show::find()
				->distinct()
				->with(['language', 'seasons', 'seasons.episodes'])
				->leftJoin(UserShow::tableName(), UserShow::tableName() . '.[[show_id]] = ' . Show::tableName() . '.[[id]]')
				->where([UserShow::tableName() . '.[[user_id]]' => Yii::$app->user->id]),
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
	 * @return array|UserEpisode[]
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

		if ($show !== null)
			return $show->getLastEpisodes(Yii::$app->user->id)->all();
		else
			return [];
	}

	/**
	 * Subscribe to tv show.
	 *
	 * @param integer $id
	 * @param string $iso
	 *
	 * @return Show
	 */
	public function actionShowSubscribe($id, $iso)
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

		$userShow = UserShow::find()
			->where([
				'show_id' => $show->id,
				'user_id' => Yii::$app->user->id,
			])
			->one();

		if ($userShow !== null) {
			if ($userShow->deleted_at !== null) {
				$userShow->deleted_at = null;
				$userShow->save();
			}

			return [
				'success' => true,
			];
		}

		$userShow = new UserShow;
		$userShow->show_id = $show->id;
		$userShow->user_id = Yii::$app->user->id;
		if ($userShow->save()) {
			return [
				'success' => true,
			];
		} else {
			Yii::error('Could not save user show for user #{$userShow->user_id} and show #{$userShow->show_id}: ' . serialize($userShow->errors), 'application\api\v1');

			return [
				'success' => false,
			];
		}
	}

	/**
	 * Unsubscribe from a tv show.
	 *
	 * @param integer $id
	 * @param string $iso
	 *
	 * @return Show
	 */
	public function actionShowUnsubscribe($id, $iso)
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

		$userShow = UserShow::find()
			->where([
				'show_id' => $show->id,
				'user_id' => Yii::$app->user->id,
			])
			->one();

		if ($userShow !== null)
			$userShow->delete();

		return [
			'success' => true,
		];
	}

	public function actionEpisodeWatch($id, $iso, $season, $episode)
	{
		$episode = Episode::findBySql('
			SELECT
				{{%episode}}.*
			FROM
				{{%episode}},
				{{%season}},
				{{%show}},
				{{%language}}
			WHERE
				{{%show}}.[[themoviedb_id]] = :id AND
				{{%language}}.[[iso]] = :iso AND
				{{%show}}.[[language_id]] = {{%language}}.[[id]] AND
				{{%season}}.[[show_id]] = {{%show}}.[[id]] AND
				{{%episode}}.{{season_id}} = {{%season}}.[[id]] AND
				{{%season}}.[[number]] = :season AND
				{{%episode}}.[[number]] = :episode
		', [
			':id' => $id,
			':iso' => $iso,
			':season' => $season,
			':episode' => $episode,
		])
			->one();

		if ($episode === null)
			throw new \yii\web\NotFoundHttpException("The episode could not be found!");

		$run = UserShowRun::find()
			->where([
				'show_id' => $episode->season->show_id,
				'user_id' => Yii::$app->user->id
			])
			->one();
		if ($run === null) {
			$run = new UserShowRun;
			$run->show_id = $episode->season->show_id;
			$run->user_id = Yii::$app->user->id;

			if (!$run->save()) {
				Yii::error('Could not save new user show run for user #{$run->user_id} and show #{$run->show_id}: ' . serialize($run->errors), 'application\api\v1');

				return [
					'success' => false,
				];
			}
		}

		$userEpisode = UserEpisode::find()
			->where([
				'episode_id' => $episode->id,
				'run_id' => $run->id,
			])
			->one();
		if ($userEpisode !== null)
			return [
				'success' => true,
			];

		$userEpisode = new UserEpisode;
		$userEpisode->episode_id = $episode->id;
		$userEpisode->run_id = $run->id;

		if ($userEpisode->save()) {
			return [
				'success' => true,
			];
		} else {
			Yii::error('Could not save user episode for episode #{$userEpisode->episode_id} and run #{$userEpisode->run_id}: ' . serialize($userEpisode->errors), 'application\api\v1');

			return [
				'success' => false,
			];
		}
	}

	public function actionEpisodeUnwatch($id, $iso, $season, $episode)
	{
		$episode = Episode::findBySql('
			SELECT
				{{%episode}}.*
			FROM
				{{%episode}},
				{{%season}},
				{{%show}},
				{{%language}}
			WHERE
				{{%show}}.[[themoviedb_id]] = :id AND
				{{%language}}.[[iso]] = :iso AND
				{{%show}}.[[language_id]] = {{%language}}.[[id]] AND
				{{%season}}.[[show_id]] = {{%show}}.[[id]] AND
				{{%episode}}.{{season_id}} = {{%season}}.[[id]] AND
				{{%season}}.[[number]] = :season AND
				{{%episode}}.[[number]] = :episode
		', [
			':id' => $id,
			':iso' => $iso,
			':season' => $season,
			':episode' => $episode,
		])
			->one();

		if ($episode === null)
			throw new \yii\web\NotFoundHttpException("The episode could not be found!");

		$run = UserShowRun::find()
			->where([
				'show_id' => $episode->season->show_id,
				'user_id' => Yii::$app->user->id
			])
			->one();
		if ($run === null) {
			return [
				'success' => true,
			];
		}

		$userEpisode = UserEpisode::find()
			->where([
				'episode_id' => $episode->id,
				'run_id' => $run->id,
			])
			->one();
		if ($userEpisode === null)
			return [
				'success' => true,
			];

		$userEpisode->delete();

		return [
			'success' => true,
		];
	}
}
