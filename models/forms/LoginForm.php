<?php namespace app\models\forms;

use \Yii;
use \yii\base\Model;

use \app\models\User;
use \app\components\YiiMixpanel;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
	public $email;
	public $password;
	public $rememberMe = true;

	private $_user = null;

	/**
	 *	Define validation rules.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			[['email', 'password'], 'required'],
			['rememberMe', 'boolean'],
			['password', 'validatePassword'],
		];
	}

	/**
	 * Validates the password.
	 * This method serves as the inline validation for password.
	 *
	 * @return void
	 */
	public function validatePassword()
	{
		if (!$this->hasErrors()) {
			$user = $this->getUser();

			if ($user === null || !$user->validatePassword($this->password)) {
				$this->addError('password', Yii::t('User/LoginForm', 'Incorrect email or password.'));
			}
		}
	}

	/**
	 * Logs in a user using the provided email and password.
	 *
	 * @return boolean whether the user is logged in successfully
	 */
	public function login()
	{
		if ($this->validate()) {
			YiiMixpanel::createAlias(\session_id(), $this->getUser()->id);

			return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
		} else {
			return false;
		}
	}

	/**
	 * Finds user by [[email]]
	 *
	 * @return User|null
	 */
	public function getUser()
	{
		if ($this->_user === null)
			$this->_user = User::findByEmail($this->email);

		return $this->_user;
	}
}
