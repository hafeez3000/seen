<?php namespace app\models\forms;

use \Yii;
use \yii\base\Model;

use \app\models\User;

/**
 * PasswordResetSendForm is the model behind the password reset send form.
 */
class PasswordResetForm extends Model
{
	private $token;
	public $password;

	/**
	 *	Define validation rules.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			[['password'], 'required'],
			[['password'], 'string', 'min' => 6],
		];
	}

	public function __construct($token)
	{
		$this->token = $token;

		parent::__construct();
	}

	/**
	 * Generate a reset token and send the token to the user.
	 *
	 * @return void
	 */
	public function reset()
	{
		$user = User::findByResetKey($this->token);
		$user->reset_key = '';
		$user->setPassword($this->password);
		return $user->save();
	}
}
