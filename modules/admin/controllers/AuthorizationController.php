<?php namespace app\modules\admin\controllers;

use \Yii;
use \yii\filters\AccessControl;

use \app\models\User;
use \app\modules\admin\models\AuthItem;
use \app\modules\admin\models\AuthItemSearch;

class AuthorizationController extends BaseController
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['index', 'view', 'load', 'add', 'remove'],
				'rules' => [
					[
						'actions' => ['index', 'view', 'load', 'add', 'remove'],
						'allow' => true,
						'roles' => ['admin'],
					],
				],
			],
		];
	}

	public function actionIndex()
	{
		$filterModel = new AuthItemSearch;
		$dataProvider = $filterModel->search($_GET);

		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'filterModel' => $filterModel,
		]);
	}

	public function actionView($id)
	{
		$model = AuthItem::find()
			->with('users')
			->where([
				'name' => $id,
			])
			->one();
		if ($model === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Authorization', 'The authoization item {id} could not be found!', [
				'id' => $id,
			]));

		return $this->render('view', [
			'model' => $model,
		]);
	}

	public function actionLoad()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		if (Yii::$app->request->get('term', false) !== false) {
			return User::find()
				->select([
					'[[email]] AS [[label]]'
				])
				->where(['like', 'email', Yii::$app->request->get('term')])
				->asArray()
				->all();
		} else {
			throw new \yii\web\BadRequestHttpException(Yii::t('Authorization', 'Missing parameter "term"'));
		}
	}

	public function actionAdd()
	{
		$data = Yii::$app->request->post('AuthItem', false);
		if ($data === false)
			throw new \yii\web\BadRequestHttpException(Yii::t('Authorization', 'Missing parameter "AuthItem"'));

		if (!isset($data['user']) || !isset($data['name']))
			throw new \yii\web\BadRequestHttpException(Yii::t('Authorization', 'Missing parameter "AuthItem[user]" or "AuthItem[name]"'));

		$authItem = AuthItem::find()->where(['name' => $data['name']])->one();
		if ($authItem === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Authorization', 'Unknown auth item {id}', [
				'id' => $data['name'],
			]));

		$user = User::find()->where(['email' => $data['user']])->one();
		if ($user === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Authorization', 'Unknown user {email}', [
				'email' => $data['user'],
			]));

		$exists = Yii::$app->db->createCommand('
			SELECT COUNT(*) as [[count]]
			FROM {{%auth_assignment}}
			WHERE [[item_name]] = :name AND [[user_id]] = :user_id
		')
			->bindValue(':name', $authItem->name)
			->bindValue(':user_id', $user->id)
			->queryOne();

		if ($exists['count'] <= 0) {
			Yii::$app->db->createCommand()
				->insert('{{%auth_assignment}}', [
					'item_name' => $authItem->name,
					'user_id' => $user->id,
				])
				->execute();
			Yii::$app->session->setFlash('success', Yii::t('Authorization', 'User successfully assigned to the role.'));
		} else {
			Yii::$app->session->setFlash('warning', Yii::t('Authorization', 'The user is already assigned to the role!'));
		}

		return $this->redirect(['view', 'id' => $authItem->name]);
	}

	public function actionRemove($auth, $user)
	{
		$authItem = AuthItem::find()->where(['name' => $auth])->one();
		if ($authItem === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Authorization', 'Unknown auth item {id}', [
				'id' => $auth,
			]));

		$user = User::find()->where(['id' => $user])->one();
		if ($user === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Authorization', 'Unknown user {email}', [
				'email' => $user,
			]));

		Yii::$app->db->createCommand()->delete('{{%auth_assignment}}', [
			'item_name' => $authItem->name,
			'user_id' => $user->id,
		])->execute();

		Yii::$app->session->setFlash('success', Yii::t('Authorization', 'Role successfully removed from the user.'));

		return $this->redirect(['view', 'id' => $authItem->name]);
	}
}
