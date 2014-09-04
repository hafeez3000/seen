<?php namespace tests\unit\models;

use \Yii;
use \yii\codeception\DbTestCase;
use \Codeception\Util\Debug;

use \app\models\User;
use \app\models\forms\AccountForm;
use \app\tests\codeception\unit\fixtures\UserFixture;

class AccountFormTest extends DbTestCase
{
	use \Codeception\Specify;

	public function fixtures()
	{
		return [
			'users' => UserFixture::className(),
		];
	}

	protected function tearDown()
	{
		Yii::$app->user->logout();
		parent::tearDown();
	}

	public function testUpdateUser()
	{
		$defaultUser = $this->users('user1');
		Yii::$app->user->login($defaultUser);

		$model = new AccountForm($defaultUser);

		$model->email = 'tom_test2@seenapp.com';

		$this->specify('user should be updated in the database', function() use ($model, $defaultUser) {
			expect('form should save account', $model->save())->true();

			$user = User::findByEmail('tom_test2@seenapp.com');
			expect('user should exist with new email', $user)->notNull();
			expect('name should be the same', $defaultUser->name)->equals($user->name);
			expect('current user email should have updated', $user->email)->equals(Yii::$app->user->identity->email);
		});
	}
}
