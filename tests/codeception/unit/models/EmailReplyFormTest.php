<?php namespace tests\unit\models;

use \Yii;
use \yii\codeception\DbTestCase;

use \app\models\Email;
use \app\models\forms\EmailReplyForm;
use \app\tests\codeception\unit\fixtures\UserFixture;

class EmailReplyFormTest extends DbTestCase
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

	public function testConstructor()
	{
		$email = new Email;
		$email->from_email = 'test@seenapp.com';
		$model = new EmailReplyForm($email);

		$this->assertEquals($email->from_email, $model->receiver);

		$email = new Email;
		$email->from_email = 'test@seenapp.com';
		$email->from_name = 'Tom Test';
		$model = new EmailReplyForm($email);
		$this->assertEquals('Tom Test <' . $email->from_email . '>', $model->receiver);
	}

	public function testDefaultSubject()
	{
		$email = new Email;
		$email->from_email = 'test@seenapp.com';
		$email->subject = 'Previous subject';

		$model = new EmailReplyForm($email);

		$this->assertTrue(strpos($model->defaultSubject, $email->subject) > 0);
		$this->assertEquals($model->defaultSubject, $model->subject);
	}

	public function testReplyNoUser()
	{
		$email = new Email;
		$email->from_email = 'test@seenapp.com';
		$model = new EmailReplyForm($email);
		$this->assertFalse($model->reply());
	}

	public function testReplyUser()
	{
		Yii::$app->user->setIdentity($this->users('user1'));

		$email = new Email;
		$email->from_email = 'test@seenapp.com';
		$model = new EmailReplyForm($email);
		$this->assertTrue($model->reply());
	}

	public function testValidation()
	{
		$email = new Email;
		$email->from_email = 'test@seenapp.com';
		$model = new EmailReplyForm($email);
		$this->assertFalse($model->validate());

		$model->text = 'Test';
		$this->assertTrue($model->validate());
	}
}
