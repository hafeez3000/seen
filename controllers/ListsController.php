<?php namespace app\controllers;

use Yii;
use \yii\filters\AccessControl;
use \yii\web\Controller;
use \yii\filters\VerbFilter;

use \app\models\Lists;

/**
 * ListsController implements the CRUD actions for Lists model.
 */
class ListsController extends Controller
{
	public function behaviors()
	{
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['post'],
				],
			],
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['create', 'update', 'delete'],
				'rules' => [
					[
						'actions' => ['create', 'update', 'delete'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
	}

	/**
	 * Lists all Lists models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		$lists = Lists::find()
			->where([
				'public' => 1,
				'highlighted' => 1
			])
			->orWhere([
				'user_id' => Yii::$app->user->id
			])
			->with('lastEntry')
			->orderBy(['created_at' => SORT_DESC])
			->limit(20)
			->all();

		return $this->render('index', [
			'lists' => $lists,
		]);
	}

	/**
	 * Displays a single lists.
	 *
	 * @param string $slug
	 *
	 * @return mixed
	 */
	public function actionView($slug)
	{
		$model = $this->findModel($slug);

		return $this->render('view', [
			'model' => $model,
		]);
	}

	/**
	 * Creates a new Lists model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate()
	{
		$model = new Lists();

		if (Yii::$app->request->isPost) {
			$model->load(Yii::$app->request->post());
			$model->user_id = Yii::$app->user->id;
			$model->title = strip_tags($model->title);
			$model->description = strip_tags($model->description);
			$model->slug = '';
			if (!Yii::$app->user->can('admin'))
				$model->highlighted = false;

			if ($model->save())
				return $this->redirect(['view', 'slug' => $model->slug]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing Lists model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate($slug)
	{
		$model = $this->findModel($slug);

		//Top 100 movies of 2014 according to [imdb.com](http://www.imdb.com/search/title?at=0&sort=moviemeter&title_type=feature&year=2014,2014).

		if (Yii::$app->request->isPost) {
			$post = Yii::$app->request->post('Lists', []);

			$model->title = strip_tags(isset($post['title']) ? $post['title'] : '');
			$model->description = strip_tags(isset($post['description']) ? $post['description'] : '');
			$model->public = isset($post['public']) ? $post['public'] : true;
			if (Yii::$app->user->can('admin'))
				$model->highlighted = isset($post['highlighted']) ? $post['highlighted'] : false;

			if ($model->save()) {
				return $this->redirect(['view', 'slug' => $model->slug]);
			}
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing Lists model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete($id)
	{
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Get a list by its slug.
	 *
	 * @param string $slug
	 *
	 * @return \app\models\Lists
	 */
	protected function findModel($slug)
	{
		$model = Lists::find()
			->with(['user'])
			->where(['slug' => $slug]);

		if (!Yii::$app->user->can('admin'))
			$model->andWhere([
				'or',
					['public' => true],
					['user_id' => Yii::$app->user->id]
			]);

		$model = $model->one();

		if ($model === null)
			throw new \yii\web\HttpNotFundException(Yii::t('Lists', 'The list could not be found!'));

		return $model;
	}
}
