<?php namespace app\controllers;

use \Yii;
use \yii\web\Controller;
use \yii\web\Response;

use \app\models\Person;
use \app\components\MovieDb;
use \app\components\YiiMixpanel;

class PersonController extends Controller
{
	/**
	 * Disable CSRF validation for ajax requests.
	 *
	 * @param yii\base\Action $action
	 *
	 * @return boolean
	 */
	public function beforeAction($action)
	{
		if (Yii::$app->request->isAjax)
			$this->enableCsrfValidation = false;

		return parent::beforeAction($action);
	}

	/**
	 * Displays a person
	 *
	 * @param string $slug
	 *
	 * @return string
	 */
	public function actionView($slug)
	{
		if (is_numeric($slug)) {
			$person = Person::find()
				->where(['id' => $slug])
				->one();

			if ($person !== null && !empty($person->slug)) {
				return $this->redirect(['person/view', 'slug' => $person->slug], 301);
			}
		} else {
			$person = Person::find()
				->where(['slug' => $slug])
				->with([
					'aliases'
				])
				->one();
		}

		if ($person === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Person/View', 'The person could not be found!'));

		$movies = $person->getMovies()->with('userWatches')->all();
		$shows = $person->getShows()->with('userShows')->all();

		YiiMixpanel::track('Show Person');

		return $this->render('view', [
			'person' => $person,
			'shows' => $shows,
			'movies' => $movies,
		]);
	}

	/**
	 * Load a person from TheMovieDB.
	 *
	 * @return array
	 */
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
				'url' => Yii::$app->urlManager->createAbsoluteUrl(['/person/view', 'slug' => $person->slug])
			];

		$person = new Person;
		$person->id = Yii::$app->request->post('id');
		$person->save();

		$movieDb = new MovieDb;

		YiiMixpanel::track('Person Load');

		if ($movieDb->syncPerson($person)) {
			return [
				'success' => true,
				'url' => Yii::$app->urlManager->createAbsoluteUrl(['/person/view', 'slug' => $person->slug])
			];
		} else {
			return [
				'success' => false,
				'message' => Yii::t('Person', 'The Person could not be loaded at the moment! Please try again later.'),
			];
		}
	}
}
