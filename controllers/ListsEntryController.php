<?php namespace app\controllers;

use Yii;
use \yii\filters\AccessControl;
use \yii\web\Controller;
use \yii\filters\VerbFilter;

use \app\models\Lists;
use \app\models\ListsEntry;

/**
 * ListsController implements the CRUD actions for Lists model.
 */
class ListsEntryController extends Controller
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['create', 'update', 'delete', 'up', 'down'],
				'rules' => [
					[
						'actions' => ['create', 'update', 'delete', 'up', 'down'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
	}

	/**
	 * Append a new entry to a list.
	 *
	 * @return mixed
	 */
	public function actionCreate($slug)
	{
		$list = $this->findModel($slug, ['user', 'entries']);
		$model = new ListsEntry();

		if (Yii::$app->request->isPost) {
			$data = Yii::$app->request->post('ListsEntry', []);

			if (!isset($data['type']) || empty($data['type']))
				throw new \yii\web\BadRequestHttpException('Missing parameter: type');

			$type = null;
			switch ($data['type']) {
				case 'movie':
					$type = ListsEntry::TYPE_MOVIE;
					break;
				case 'tv':
					$type = ListsEntry::TYPE_TV_SHOW;
					break;
				case 'person':
					$type = ListsEntry::TYPE_PERSON;
					break;
				default:
					throw new \yii\web\BadRequestHttpException(sprintf('Unknown media type %s', $data['type']));
			}

			$position = (isset($data['position']) && !empty($data['position'])) ? $data['position'] + 1 : 1;

			$model->list_id = $list->id;
			$model->type = $type;
			$model->themoviedb_id = $data['themoviedb_id'];
			$model->description = strip_tags($model->description);
			$model->position = $position;
			if (!Yii::$app->user->can('admin'))
				$model->highlighted = false;

			if ($model->save()) {
				$command = Yii::$app->db->createCommand('
					UPDATE
						{{%list_entry}}
					SET
						[[position]] = [[position]] + 1
					WHERE
						[[list_id]] = :list_id AND
						[[position]] >= :position AND
						[[id]] != :list_entry_id
					');
				$command->bindValue(':list_id', $list->id);
				$command->bindValue(':position', $model->position);
				$command->bindValue(':list_entry_id', $model->id);
				$command->execute();

				return $this->redirect(['/lists/view', 'slug' => $list->slug]);
			}
		}

		return $this->render('create', [
			'model' => $model,
			'list' => $list,
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
	protected function findModel($slug, $with = ['user'])
	{
		$model = Lists::find()
			->with($with)
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
