<?php namespace app\models\forms;

use \Yii;
use \yii\base\Model;

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
			'verifyCode' => Yii::t('Contact', 'Verification Code'),
		];
	}

	/**
	 * Sends an email to the specified email address using the information collected by this model.
	 *
	 * @param string $email the target email address
	 *
	 * @return boolean whether the model passes validation
	 */
	public function contact($email)
	{
		if ($this->validate()) {
			$email = new Email;
			$email->to = Yii::$app->params['email']['admin'];
			$email->subject = Yii::t('Email/Contact', '[seen] {subject}', ['subject' => $this->subject]);

			$html = Yii::t('Email/Contact', '<h1 class="h1">{subject}</h1>', ['name' => $this->name]);
			$html .= Yii::t(
				'Email/Contact',
				'<p>Name: {name} (<a href="mailto:{email}">{email}</a>)</p><p>{body}</p>',
				array(
					'name' => $this->name,
					'email' => $this->email,
					'body' => $this->body,
				)
			);

			$email->send(
				'Default',
				array(
					array(
						'name' => 'content',
						'content' => $html,
					)
				),
				array(
					'contact',
				)
			);

			return true;
		} else {
			return false;
		}
	}
}
