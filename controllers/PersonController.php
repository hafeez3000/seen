<?php namespace app\controllers;

use \Yii;
use \yii\web\Controller;
use \yii\web\Response;

use \app\models\Person;

class PersonController extends Controller
{
	public function beforeAction($action)
	{
		if (Yii::$app->request->isAjax)
			$this->enableCsrfValidation = false;

		return parent::beforeAction($action);
	}

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

		$movies = $person->getMovies()->with('userWatches')->all();
		$shows = $person->getShows()->with('userShows')->all();

		return $this->render('view', [
			'person' => $person,
			'shows' => $shows,
			'movies' => $movies,
		]);
	}

	public function actionLoad()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		if (!Yii::$app->request->isPost || Yii::$app->request->post('id') === null)
			throw new yii\web\BadRequestHttpException;

		$person = Person::find()
			->where(['id' => Yii::$app->request->post('id')])
			->one();

		if ($person !== null)
			return [
				'success' => true,
				'url' => Yii::$app->urlManager->createAbsoluteUrl(['/person/view', 'id' => $person->id])
			];

		$person = new Person;
		$person->id = Yii::$app->request->post('id');
		$person->save();

		$movieDb = new MovieDb;

		if ($movieDb->syncPerson($person)) {
			return [
				'success' => true,
				'url' => Yii::$app->urlManager->createAbsoluteUrl(['/person/view', 'id' => $person->id])
			];
		} else {
			return [
				'success' => false,
				'message' => Yii::t('Person', 'The Person could not be loaded at the moment! Please try again later.'),
			];
		}
	}
}
