<?php namespace app\models\forms;

use \Yii;
use \yii\base\Model;

use \app\models\User;
use \app\components\Email;

/**
 * PasswordResetSendForm is the model behind the password reset send form.
 */
class PasswordResetSendForm extends Model
{
	public $email;

	/**
	 *	Define validation rules.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			[['email'], 'required'],
			[['email'], 'email'],
		];
	}

	/**
	 * Generate a reset token and send the token to the user.
	 *
	 * @return boolean
	 */
	public function send()
	{
		$user = User::findByEmail($this->email);
		if ($user === null) {
			// Pretend sending email
			sleep(1);

			return true;
		}

		$user->generateResetKey();
		$user->save();

		$email = new Email;
		$email->to = $user->email;
		$email->subject = Yii::t('Email/PasswordReset', '[SEEN] Reset your password');

		$html = Yii::t('Email/PasswordReset', '<h1 class="h1">Reset your password</h1>');
		$html .= Yii::t(
			'Email/PasswordReset',
			'<p>To reset your password please go to <a href="{url}">{url}</a>.</p>',
			array(
				'url' => Yii::$app->urlManager->createAbsoluteUrl(['/site/reset-password', 'token' => $user->reset_key]),
			)
		);

		return $email->send(
			'Default',
			array(
				array(
					'name' => 'content',
					'content' => $html,
				)
			),
			array(
				'password-reset',
			)
		);
	}
}
