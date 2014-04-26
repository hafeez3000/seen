<?php namespace app\controllers;

use \Yii;
use \yii\web\Controller;
use \yii\filters\AccessControl;
use \yii\data\Pagination;

use \app\models\Email;
use \app\models\EmailGroup;
use \app\models\forms\EmailReplyForm;

class EmailController extends Controller
{
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['index', 'groups'],
				'rules' => [
					[
						'actions' => ['index'],
						'allow' => true,
						'roles' => ['viewEmails'],
					],
					[
						'actions' => ['groups'],
						'allow' => true,
						'roles' => ['manageEmailGroups'],
					],
				],
			],
		];
	}

	public function actionIndex()
	{
		$query = '
			FROM
				{{%email}},
				{{%email_to}},
				{{%email_group}},
				{{%user_email_group}}
			WHERE
				{{%user_email_group}}.[[user_id]] = :user_id AND
				{{%email_group}}.[[id]] = {{%user_email_group}}.[[email_group_id]] AND
				{{%email_group}}.[[receiver]] = {{%email_to}}.[[to_email]] AND
				{{%email}}.[[id]] = {{%email_to}}.[[email_id]] AND
				{{%email}}.[[success]] = 1
			ORDER BY
				{{%email}}.[[ts]] DESC';

		$countQuery = Yii::$app->db->createCommand('SELECT COUNT(DISTINCT {{%email}}.[[id]]) AS [[count]] ' . $query)
			->bindValue(':user_id', Yii::$app->user->id)
			->queryOne();

		$pages = new Pagination([
			'totalCount' => $countQuery['count'],
		]);

		$emails = Email::findBySql('SELECT DISTINCT {{%email}}.* ' . $query . ' LIMIT :offset, :limit', [
			':user_id' => Yii::$app->user->id,
			':limit' => $pages->limit,
			':offset' => $pages->offset,
		])
			->with('responded')
			->with('assigned')
			->with('to')
			->all();

		$groups = EmailGroup::find()
			->leftJoin('{{%user_email_group}}', ['user_id' => Yii::$app->user->id])
			->all();

		return $this->render('index', [
			'pages' => $pages,
			'emails' => $emails,
			'groups' => $groups,
		]);
	}

	public function actionReply($id)
	{
		$email = Email::findOne($id);
		if ($email === null)
			throw new \yii\web\NotFoundHttpException(Yii::t('Email', 'The email could not be found!'));

		if (!Yii::$app->user->can('replyAllEmails') && !Yii::$app->user->can('replyEmailsInGroup', ['groups' => $email->groups]))
			throw new \yii\web\ForbiddenHttpException(Yii::t('Email', 'You are not allowed to reply to this email!'));

		$model = new EmailReplyForm($email);

		if ($model->load(Yii::$app->request->post()) && $model->reply()) {
			return $this->redirect(['email/index']);
		} else {
			$emails = Email::find()
				->where('id != :id', [':id' => $email->id])
				->andWhere(['from_email' => $email->from_email])
				->all();

			return $this->render('reply', [
				'model' => $model,
				'email' => $email,
				'emails' => $emails,
			]);
		}
	}

	public function actionGroups()
	{
		// Implement
	}
}
