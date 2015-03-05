<?php namespace app\controllers;

use \Yii;
use \yii\filters\AccessControl;
use \yii\web\Controller;
use \yii\web\Response;

use \app\models\Show;
use \app\models\Language;
use \app\models\UserShow;
use \app\models\UserShowRating;

use \app\components\MovieDb;

class TvController extends Controller
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
				'only' => ['subscribe', 'unsubscribe', 'archive', 'archiveShow', 'unarchiveShow', 'sync', 'recommend', 'rate'],
				'rules' => [
					[
						'actions' => ['subscribe', 'unsubscribe', 'archive', 'archiveShow', 'unarchiveShow', 'recommend', 'rate'],
						'allow' => true,
						'roles' => ['@'],
					],
					[
						'actions' => ['sync'],
						'allow' => true,
						'roles' => ['admin'],
					]
				],
			],
		];
	}

	public function actionIndex()
	{
		if (Yii::$app->user->isGuest) {
			return $this->actionPopular();
		} else {
			return $this->actionDashboard();
		}
	}

	public function actionPopular()
	{
		$language = Language::find()
			->where(['iso' => Yii::$app->language])
			->orWhere(['iso' => Yii::$app->params['lang']['default']])
			->one();

		if ($language === null)
			$language = Language::find()
				->one();

		$shows = Show::popular($language->id)
			->all();

		Show::warmLatestEpisodeCache(null, $shows);

		return $this->render('popular', [
			'shows' => $shows,
		]);
	}

	public function actionArchive()
	{
		$cacheId = 'archive-tv-' . Yii::$app->user->id;
		$shows = Yii::$app->cache->get($cacheId);

		if ($shows === false) {
			$shows = Yii::$app->user->identity
				->getArchivedShows()
				->all();

			Show::warmLatestEpisodeCache(null, $shows);

			usort($shows, function($a, $b) {
				$a = $a->getLastEpisode(null, true);
				$b = $b->getLastEpisode(null, true);

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
					'user-tv-' . Yii::$app->user->id,
				]
			]);
			Yii::$app->cache->set($cacheId, $shows, 0, $dependency);
		} else {
			Show::warmLatestEpisodeCache(null, $shows);
		}

		return $this->render('archive', [
			'shows' => $shows,
		]);
	}

	public function actionDashboard()
	{
		$cacheId = 'dashboard-tv-' . Yii::$app->user->id;
		$shows = Yii::$app->cache->get($cacheId);

		if ($shows === false) {
			$shows = Yii::$app->user->identity
				->getShows()
				->all();

			Show::warmLatestEpisodeCache(null, $shows);

			usort($shows, function($a, $b) {
				$a = $a->getLastEpisode(null, true);
				$b = $b->getLastEpisode(null, true);

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
					'user-tv-' . Yii::$app->user->id,
				]
			]);
			Yii::$app->cache->set($cacheId, $shows, 0, $dependency);
		} else {
			Show::warmLatestEpisodeCache(null, $shows);
		}

		return $this->render('dashboard', [
			'shows' => $shows,
		]);
	}

	public function actionView($slug)
	{
		$show = Show::find()
			->where(['slug' => $slug])
			->with([
				'seasons',
				'seasons.episodes',
				'creators',
				'cast',
				'cast.person',
				'crew',
				'crew.person',
				'language',
				'genres',
			])
			->one();
		if ($show === null) {
			$searchSlug = implode('-', array_filter(explode('-', $slug), function($item) {
				return !is_numeric($item);
			}));

			if (!empty($searchSlug)) {
				$show = Show::find()
					->where(['like', 'slug', $searchSlug])
					->with([
						'seasons',
						'seasons.episodes',
						'creators',
						'cast',
						'cast.person',
						'crew',
						'crew.person',
						'language',
						'genres',
					])
					->one();

				if ($show !== null)
					return $this->redirect(['view', 'slug' => $show->slug], 301);
			}
		}

		if ($show === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Show', 'The TV Show could not be found!'));

		if ($show->language->iso != Yii::$app->language) {
			$showNative = Show::find()
				->select('{{%show}}.*')
				->distinct()
				->from([
					'{{%show}}',
					'{{%language}}',
				])
				->where([
					'themoviedb_id' => $show->themoviedb_id,
				])
				->andWhere('{{%show}}.[[language_id]] = {{%language}}.[[id]]')
				->andWhere('{{%language}}.[[iso]] = :language')
				->with([
					'seasons',
					'seasons.episodes',
					'creators',
					'cast',
					'cast.person',
					'crew',
					'crew.person',
					'language',
					'genres',
				])
				->params([
					':language' => Yii::$app->language,
				])
				->one();
		} else {
			$showNative = null;
		}

		if (!Yii::$app->user->isGuest)
			$userRating = UserShowRating::find()
				->where(['user_id' => Yii::$app->user->id])
				->where(['themoviedb_id' => $show->themoviedb_id])
				->one();
		else
			$userRating = null;

		return $this->render('view', [
			'show' => $show,
			'showNative' => $showNative,
			'userRating' => $userRating,
		]);
	}

	public function actionLoad()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		if (!Yii::$app->request->isPost || Yii::$app->request->post('id') === null)
			throw new yii\web\BadRequestHttpException;

		$language = Language::find()
			->where(['iso' => Yii::$app->language])
			->one();

		if ($language === null)
			$language = Language::find()
				->where(['iso' => Yii::$app->params['lang']['default']])
				->one();

		$show = Show::find()
			->where(['themoviedb_id' => Yii::$app->request->post('id')])
			->andWhere(['language_id' => $language->id])
			->one();

		if ($show !== null)
			return [
				'success' => true,
				'url' => Yii::$app->urlManager->createAbsoluteUrl(['/tv/view', 'slug' => $show->slug])
			];

		$show = new Show;
		$show->themoviedb_id = Yii::$app->request->post('id');
		$show->language_id = $language->id;
		$show->save();

		$movieDb = new MovieDb;

		$show->slug = ''; // Rewrite slug with title
		if ($movieDb->syncShow($show)) {
			foreach ($show->seasons as $season) {
				$movieDb->syncSeason($season);
			}

			return [
				'success' => true,
				'url' => Yii::$app->urlManager->createAbsoluteUrl(['/tv/view', 'slug' => $show->slug])
			];
		} else {
			return [
				'success' => false,
				'message' => Yii::t('Show', 'The TV Show could not be loaded at the moment! Please try again later.'),
			];
		}
	}

	public function actionSubscribe($slug)
	{
		$show = Show::find()
			->where(['slug' => $slug])
			->one();
		if ($show === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Show', 'The TV Show could not be found!'));

		if ($show->isUserSubscribed) {
			Yii::$app->session->setFlash('info', Yii::t('Show', 'You are already subscribed to {name}.', ['name' => $show->completeName]));

			return $this->redirect(['view', 'slug' => $show->slug]);
		}

		$userShow = $show->getUserShows()
			->where(['user_id' => Yii::$app->user->id])
			->one();

		if ($userShow !== null) {
			$userShow->deleted_at = null;
			$userShow->save();
		} else {
			$userShow = new UserShow;
			$userShow->show_id = $show->id;
			$userShow->user_id = Yii::$app->user->id;
			$userShow->save();
		}

		return $this->redirect(['view', 'slug' => $show->slug]);
	}

	public function actionUnsubscribe($slug)
	{
		$show = Show::find()
			->where(['slug' => $slug])
			->one();
		if ($show === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Show', 'The TV Show could not be found!'));

		if (!$show->isUserSubscribed) {
			Yii::$app->session->setFlash('info', Yii::t('Show', 'You are not subscribed to {name}.', ['name' => $show->completeName]));

			return $this->redirect(['view', 'slug' => $show->slug]);
		}

		$userShow = UserShow::find()
			->where(['user_id' => Yii::$app->user->id])
			->andWhere(['show_id' => $show->id])
			->one();
		$userShow->delete();

		return $this->redirect(['view', 'slug' => $show->slug]);
	}

	public function actionArchiveShow($slug)
	{
		$show = Show::find()
			->where(['slug' => $slug])
			->one();
		if ($show === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Show', 'The TV Show could not be found!'));

		if (!$show->isUserSubscribed) {
			Yii::$app->session->setFlash('info', Yii::t('Show', 'You are not subscribed to {name}.', ['name' => $show->completeName]));

			return $this->redirect(['view', 'slug' => $show->slug]);
		}

		$userShow = UserShow::find()
			->where(['user_id' => Yii::$app->user->id])
			->andWhere(['show_id' => $show->id])
			->andWhere(['archived' => 0])
			->one();
		if ($userShow === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Show', 'The show is already archived!'));

		$userShow->archived = true;
		if ($userShow->save()) {
			\yii\caching\TagDependency::invalidate(Yii::$app->cache, ['user-tv-' . Yii::$app->user->id]);

			if (Yii::$app->request->isAjax) {
				Yii::$app->response->format = Response::FORMAT_JSON;

				return [
					'success' => true,
				];
			} else {
				Yii::$app->session->setFlash('success', Yii::t('Show', 'You successfully archived `{name}`. Move on to the <a href="{archive}">Archive</a> to see your archived shows.', [
					'name' => $show->completeName,
					'archive' => Yii::$app->urlManager->createAbsoluteUrl(['tv/archive']),
				]));

				return $this->redirect(['index']);
			}
		} else {
			Yii::error("User #{Yii::$app->user->id} could not archive show #{$show->id}");

			if (Yii::$app->request->isAjax) {
				Yii::$app->response->format = Response::FORMAT_JSON;

				return [
					'success' => false,
					'message' => Yii::t('Show', 'The show could not be archived!'),
				];
			} else {
				return $this->redirect(['index']);
			}
		}
	}

	public function actionUnarchiveShow($slug)
	{
		$show = Show::find()
			->where(['slug' => $slug])
			->one();
		if ($show === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Show', 'The TV Show could not be found!'));

		if (!$show->isUserSubscribed) {
			Yii::$app->session->setFlash('info', Yii::t('Show', 'You are not subscribed to {name}.', ['name' => $show->completeName]));

			return $this->redirect(['view', 'slug' => $show->slug]);
		}

		$userShow = UserShow::find()
			->where(['user_id' => Yii::$app->user->id])
			->andWhere(['show_id' => $show->id])
			->andWhere(['archived' => 1])
			->one();
		if ($userShow === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Show', 'The show is not archived!'));

		$userShow->archived = false;
		if ($userShow->save()) {
			\yii\caching\TagDependency::invalidate(Yii::$app->cache, ['user-tv-' . Yii::$app->user->id]);

			if (Yii::$app->request->isAjax) {
				Yii::$app->response->format = Response::FORMAT_JSON;

				return [
					'success' => true,
				];
			} else {
				Yii::$app->session->setFlash('success', Yii::t('Show', 'You successfully unarchived `{name}`.', [
					'name' => $show->completeName,
				]));

				return $this->redirect(['index']);
			}
		} else {
			Yii::error("User #{Yii::$app->user->id} could not unarchive show #{$show->id}");

			if (Yii::$app->request->isAjax) {
				Yii::$app->response->format = Response::FORMAT_JSON;

				return [
					'success' => false,
					'message' => Yii::t('Show', 'The show could not be unarchived!'),
				];
			} else {
				return $this->redirect(['index']);
			}
		}
	}

	public function actionSync($themoviedbId)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$shows = Show::find()
			->where(['themoviedb_id' => $themoviedbId])
			->with('seasons')
			->all();

		$movieDb = new MovieDb;
		$result = [
			'shows' => 0,
			'seasons' => 0,
			'success' => true,
		];

		foreach ($shows as $show) {
			$movieDb->syncShow($show);
			$result['shows']++;

			foreach ($show->seasons as $season) {
				$movieDb->syncSeason($season);
				$result['seasons']++;
			}
		}

		return $result;
	}

	public function actionRecommend()
	{
		$shows = Show::getRecommend()->all();

		Show::warmLatestEpisodeCache(null, $shows);

		return $this->render('recommendations', [
			'shows' => $shows,
		]);
	}

	/**
	 * Rate a tv show.
	 *
	 * @param string $slug
	 * @param int $rating
	 */
	public function actionRate($slug, $rating)
	{
		if ($rating < 1 || $rating > 10)
			throw new \yii\web\BadRequestHttpException(Yii::t('Show/Rating', 'The rating has to be between 1 and 10'));

		$show = Show::find()
			->where(['slug' => $slug])
			->one();

		if ($show === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Show/Rating', 'The tv show could not be found!'));

		$showRating = UserShowRating::find()
			->where(['user_id' => Yii::$app->user->id])
			->where(['themoviedb_id' => $show->themoviedb_id])
			->one();

		if ($showRating === null) {
			$showRating = new UserShowRating;
			$showRating->user_id = Yii::$app->user->id;
			$showRating->themoviedb_id = $show->themoviedb_id;
		}

		$showRating->rating = $rating;

		if (Yii::$app->user->identity->hasTheMovieDBAccount()) {
			$themoviedb = new MovieDb;
			if (!$themoviedb->rateTv(Yii::$app->user->identity, $showRating->themoviedb_id, $showRating->rating)) {
				$showRating->sync = false;
				Yii::$app->session->setFlash('warning', Yii::t('Show/Rating', 'Your rating could not be synced with themoviedb'));
			}
		}

		$showRating->sync = true;
		$showRating->save();

		Yii::$app->session->setFlash('success', Yii::t('Show/Rating', 'You successfully rated the tv show with {count} stars.', ['count' => $showRating->rating]));
		return $this->redirect(['view', 'slug' => $show->slug]);
	}
}
