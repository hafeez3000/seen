<?php namespace tests\unit\models;

use \Yii;
use \yii\codeception\DbTestCase;

use \app\models\forms\ContactForm;
use \app\tests\codeception\unit\fixtures\UserFixture;

class ContactFormTest extends DbTestCase
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

	public function testSendGuest()
	{
		$model = new ContactForm;

		$model->name = 'Tom Test';
		$model->email = 'tom_test@seenapp.com';
		$model->subject = 'Test';
		$model->body = 'Test';
		$model->verifyCode = 'testme';

		$this->specify('contact form should validate and send an email', function() use ($model) {
			expect('form should be validatet', $model->validate())->true();
			expect('email should be send', $model->contact())->true();
		});
	}

	public function testPrefillUser()
	{
		Yii::$app->user->login($this->users('user1'));
		$model = new ContactForm;

		$model->subject = 'Test';
		$model->body = 'Test';
		$model->verifyCode = 'testme';

		$this->specify('contact form should prefill current logged in user and send email', function() use ($model) {
			expect('user name is prefilled', $model->name)->equals($this->users('user1')->name);
			expect('user email is prefilled', $model->email)->equals($this->users('user1')->email);
			expect('email should be send', $model->contact())->true();
		});
	}

	public function testMissingEmail()
	{
		$model = new ContactForm;

		$model->name = 'Tom Test';
		$model->subject = 'Test';
		$model->body = 'Test';
		$model->verifyCode = 'testme';

		$this->specify('contact form should not validate', function() use ($model) {
			expect('email should not be sent', $model->contact())->false();
			expect('email field should be missing', $model->errors)->hasKey('email');
		});
	}

	public function testWrongCaptcha()
	{
		$model = new ContactForm;

		$model->name = 'Tom Test';
		$model->email = 'tom_test@seenapp.com';
		$model->subject = 'Test';
		$model->body = 'Test';
		$model->verifyCode = 'wrong';

		$this->specify('contact form should not validate', function() use ($model) {
			expect('email should not be sent', $model->contact())->false();
			expect('captcha should be wrong', $model->errors)->hasKey('verifyCode');
		});
	}
}
