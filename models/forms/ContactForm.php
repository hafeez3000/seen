<?php namespace app\models\forms;

use \Yii;
use \yii\base\Model;

use \app\components\Email;

/**
 * ContactForm is the model behind the contact form.
 */
class ContactForm extends Model
{
	public $name;
	public $email;
	public $subject;
	public $body;
	public $verifyCode;

	/**
	 * Define validation rules.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			[['name', 'email', 'subject', 'body'], 'required'],
			['email', 'email'],
			['verifyCode', 'captcha'],
		];
	}

	/**
	 * Define attribute labels.
	 *
	 * @return array
	 */
	public function attributeLabels()
	{
		return [
			'verifyCode' => Yii::t('Site/Contact', 'Verification Code'),
		];
	}

	public function init()
	{
		if (!Yii::$app->user->isGuest) {
			$this->name = Yii::$app->user->identity->name;
			$this->email = Yii::$app->user->identity->email;
		}

		return parent::init();
	}

	/**
	 * Sends an email to the specified email address using the information collected by this model.
	 *
	 * @return boolean whether the model passes validation
	 */
	public function contact()
	{
		if ($this->validate()) {
			$text = Yii::t(
				'Email/Contact',
				"Name: {name} ({email})\n\n{body}",
				[
					'name' => $this->name,
					'email' => $this->email,
					'body' => $this->body,
				]
			);

			return Yii::$app->mailer->compose()
				->setFrom([
					$this->email => $this->name,
				])
				->setTo(Yii::$app->params['email']['admin'])
				->setSubject(Yii::t('Email/Contact', '[seen] {subject}', ['subject' => $this->subject]))
				->setTextBody($text)
				->send();
		} else {
			return false;
		}
	}
}
