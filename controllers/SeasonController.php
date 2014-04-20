<?php namespace app\controllers;

use \Yii;
use \yii\web\Controller;

use \app\models\Show;
use \app\models\Season;

class SeasonController extends Controller
{
	public function actionView($slug, $number)
	{
		$show = Show::find()
			->where(['slug' => $slug])
			->with('seasons')
			->with('creators')
			->with('cast')
			->with('crew')
			->with('language')
			->with('seasons.episodes')
			->one();
		if ($show === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Show', 'The TV Show could not be found!'));

		$season = Season::find()
			->where(['show_id' => $show->id])
			->andWhere(['number' => $number])
			->with('episodes')
			->one();
		if ($season === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Show', 'The TV Show Season could not be found!'));

		$episodesSeen = $season
			->getLatestUserEpisodes()
			->indexBy('episode_id')
			->asArray()
			->all();

		return $this->render('view', [
			'show' => $show,
			'season' => $season,
			'episodesSeen' => $episodesSeen,
		]);
	}
}
