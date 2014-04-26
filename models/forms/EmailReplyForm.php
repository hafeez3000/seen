<?php namespace app\models\forms;

use \Yii;
use \yii\base\Model;

use \app\components\Email;

/**
 * PasswordResetSendForm is the model behind the password reset send form.
 */
class EmailReplyForm extends Model
{
	protected $email;

	public $receiver;

	public $subject;
	public $text;

	/**
	 *	Define validation rules.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			[['subject', 'text'], 'required'],
		];
	}

	public function attributeLabels()
	{
		return [
			'subject' => Yii::t('Email/EmailReplyForm', 'Subject'),
			'text' => Yii::t('Email/EmailReplyForm', 'Email content'),
		];
	}

	public function getDefaultSubject()
	{
		return 'RE: ' . $this->email->subject;
	}

	/**
	 * Create form with $email
	 *
	 * @param \app\models\Email $email
	 *
	 * @return void
	 */
	public function __construct($email)
	{
		$this->email = $email;
		$this->receiver = !empty($email->from_name) ?
			$email->from_name . ' <' . $email->from_email . '>' :
			$email->from_email;
		$this->subject = $this->defaultSubject;
	}

	/**
	 * Reply to the email with form data
	 *
	 *
	 * @return boolean
	 */
	public function reply()
	{
		$email = new Email(Yii::$app->user->identity->name, Yii::$app->user->identity->email);
		$email->to = $this->email->from_email;
		$email->to_name = $this->email->from_name;
		$email->subject = $this->subject;

		return $email->sendRaw($this->text, ['reply']);
	}
}
