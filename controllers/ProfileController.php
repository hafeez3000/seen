<?php namespace app\controllers;

use \Yii;
use \yii\filters\AccessControl;
use \yii\web\Controller;

use \app\models\User;
use \app\models\Movie;
use \app\models\UserMovie;
use \app\models\Show;
use \app\models\UserShow;
use \app\models\Language;
use \app\components\YiiMixpanel;

/**
 * Manages the views for the public user profile.
 */
class ProfileController extends Controller
{
	public function beforeAction($action)
	{
		if (Yii::$app->request->isAjax)
			$this->enableCsrfValidation = false;

		return parent::beforeAction($action);
	}

	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['tv', 'movie'],
				'rules' => [
					[
						'actions' => ['tv', 'movie'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
	}

	/**
	 * Check if $profile exists and is public
	 *
	 * @param string $profile
	 * @return User User model of the owner of the profile
	 */
	protected function authenticate($profile)
	{
		$user = User::find()
			->where('[[profile_public]] = 1 AND [[profile_name]] = :profile_name', [
				'profile_name' => $profile,
			])
			->one();

		if ($user === false) {
			throw new \yii\web\ForbiddenHttpException(Yii::t('Profile', 'The profile does not exist or is not public!'));
		}

		return $user;
	}

	public function actionIndex($profile)
	{
		$user = $this->authenticate($profile);

		// Get last seen or most popular movie
		$lastMovie = UserMovie::find()
			->where('[[user_id]] = :user_id', [':user_id' => $user->id])
			->orderBy(['[[created_at]]' => SORT_DESC])
			->limit(1)
			->one();

		if ($lastMovie === null) {
			$language = Language::find()
				->where(['iso' => Yii::$app->language])
				->orWhere(['iso' => Yii::$app->params['lang']['default_iso']])
				->one();

			$lastMovie = Movie::findBySql('
				SELECT DISTINCT
					{{%movie}}.*
				FROM
					{{%movie}},
					{{%movie_popular}}
				WHERE
					{{%movie}}.[[language_id]] = :language_id AND
					{{%movie}}.[[id]] = {{%movie_popular}}.[[movie_id]] AND
					{{%movie}}.[[title]] != ""
				ORDER BY
					{{%movie_popular}}.[[order]] ASC
				LIMIT
					1
			', [
				':language_id' => $language->id,
			])
				->one();
		} else {
			$lastMovie = $lastMovie->movie;
		}

		// Get last seen or most popular tv show
		$lastShow = UserShow::find()
			->where('[[user_id]] = :user_id', [':user_id' => $user->id])
			->orderBy(['[[created_at]]' => SORT_DESC])
			->limit(1)
			->one();

		if ($lastShow === null) {
			$language = Language::find()
				->where(['iso' => Yii::$app->language])
				->orWhere(['iso' => Yii::$app->params['lang']['default_iso']])
				->one();

			$lastShow = Movie::findBySql('
				SELECT DISTINCT
					{{%show}}.*
				FROM
					{{%show}},
					{{%show_popular}}
				WHERE
					{{%show}}.[[language_id]] = :language_id AND
					{{%show}}.[[id]] = {{%show_popular}}.[[show_id]] AND
					{{%show}}.[[title]] != ""
				ORDER BY
					{{%movie_popular}}.[[order]] ASC
				LIMIT
					1
			', [
				':language_id' => $language->id,
			])
				->one();
		} else {
			$lastShow = $lastShow->show;
		}

		YiiMixpanel::track('Visit Profile');

		return $this->render('index', [
			'user' => $user,
			'movie' => $lastMovie,
			'show' => $lastShow,
		]);
	}

	public function actionTv($profile)
	{
		$user = $this->authenticate($profile);

		$cacheId = 'profile-tv-' . $user->id;
		$shows = Yii::$app->cache->get($cacheId);

		if ($shows === false) {
			$shows = $user
				->getAllShows()
				->all();

			Show::warmLatestEpisodeCache($user->id, $shows);

			usort($shows, function($a, $b) use($user) {
				$a = $a->getLastEpisode($user->id, true);
				$b = $b->getLastEpisode($user->id, true);

				if ($a !== null && $b !== null) {
					$aTime = strtotime($a->created_at);
					$bTime = strtotime($b->created_at);

					if ($aTime == $bTime)
						return 0;

					return ($aTime < $bTime) ? 1 : -1;
				} elseif ($a === null) {
					return 1;
				} elseif ($b === null) {
					return -1;
				}

				return 0;
			});

			$dependency = new \yii\caching\TagDependency([
				'tags' => [
					'user-tv-' . $user->id,
				]
			]);
			Yii::$app->cache->set($cacheId, $shows, 0, $dependency);
		} else {
			Show::warmLatestEpisodeCache($user->id, $shows);
		}

		YiiMixpanel::track('Visit Profile TV');

		return $this->render('tv', [
			'user' => $user,
			'shows' => $shows,
		]);
	}

	public function actionMovie($profile)
	{
		$user = $this->authenticate($profile);

		$movies = Movie::find()
			->select('{{%movie}}.*')
			->from([
				'{{%movie}}',
				'{{%user_movie}}',
			])
			->where(['{{%user_movie}}.[[user_id]]' => $user->id])
			->andWhere('{{%movie}}.[[id]] = {{%user_movie}}.[[movie_id]]')
			->orderBy(['{{%user_movie}}.[[created_at]]' => SORT_DESC])
			->all();

		$watchlistDependency = new \yii\caching\TagDependency([
			'tags' => [
				'user-movie-watchlist-' . $user->id,
			]
		]);

		YiiMixpanel::track('Visit Profile Movie');

		return $this->render('movie', [
			'user' => $user,
			'movies' => $movies,
			'watchlistMovies' => Yii::$app->db->cache(function($db) use($user) {
				return Movie::getWatchlist($user->id)->all();
			}, 0, $watchlistDependency),
		]);
	}
}
