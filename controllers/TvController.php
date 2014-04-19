<?php namespace app\controllers;

use \Yii;
use \yii\filters\AccessControl;
use \yii\web\Controller;
use \yii\filters\VerbFilter;

use \app\models\Show;

class TvController extends Controller
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['add', 'logout', 'signup'],
				'rules' => [
					[
						'actions' => ['add'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'logout' => ['post'],
				],
			],
		];
	}

	public function actionIndex()
	{
		if (Yii::$app->user->isGuest) {
			return $this->render('index');
		} else {
			$shows = Yii::$app->user->identity->getShows()
				->all();

			// Load model because cannot be loaded in `usort`
			foreach ($shows as $show) {
				$show->lastEpisode;
			}

			usort($shows, function($a, $b) {
				if ($a->lastEpisode !== null && $b->lastEpisode !== null) {
					$aTime = strtotime($a->lastEpisode->created_at);
					$bTime = strtotime($b->lastEpisode->created_at);

					if ($aTime == $bTime)
						return 0;

					return ($aTime < $bTime) ? 1 : -1;
				} elseif ($a->lastEpisode === null) {
					return 1;
				} elseif ($b->lastEpisode === null) {
					return -1;
				}

				return 0;
			});

			return $this->render('dashboard', [
				'shows' => $shows,
			]);
		}
	}

	public function actionSearch()
	{
		return $this->render('search');
	}

	public function actionView($slug)
	{
		$show = Show::find()
				->where(['slug' => $slug])
				->with('seasons')
				->with('creators')
				->with('cast')
				->with('crew')
				->with('seasons.episodes');

		$show = $show->one();
		if ($show === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Show', 'The TV Show could not be found!'));

		return $this->render('view', [
			'show' => $show,
		]);
	}

	public function actionAdd()
	{

	}
}
