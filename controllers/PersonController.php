<?php namespace app\controllers;

use \Yii;
use \yii\web\Controller;

use \app\models\Person;

class PersonController extends Controller
{
	public function actionView($id)
	{
		$person = Person::find()
			->where(['id' => $id])
			->with([
				'aliases'
			])
			->one();
		if ($person === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Person/View', 'The person could not be found!'));

		$movies = $person->getMovies()->all();
		$shows = $person->getShows()->all();

		return $this->render('view', [
			'person' => $person,
			'shows' => $shows,
			'movies' => $movies,
		]);
	}
}
