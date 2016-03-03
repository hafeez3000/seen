<?php namespace app\models\forms;

use \Yii;
use \yii\base\Model;

use \app\models\User;
use \app\components\YiiMixpanel;

/**
 * SignupForm is the model behind the sign up form.
 */
class SignupForm extends Model
{
	public $email;
	public $password;

	/**
	 *	Define validation rules.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			[['email', 'password'], 'required'],
			[['email'], 'email'],
			[['password'], 'string', 'min' => 6],
			[['email'], 'unique', 'targetClass' => User::className(), 'targetAttribute' => 'email'],
		];
	}

	/**
	 * Logs in a user using the provided email and password.
	 *
	 * @return boolean whether the user is logged in successfully
	 */
	public function register()
	{
		if ($this->validate()) {
			$user = new User;
			$user->scenario = 'register';
			$user->email = $this->email;
			$user->setPassword($this->password);
			$user->save();

			YiiMixpanel::createAlias(\session_id(), $user->id);

			return Yii::$app->user->login($user, 3600*24*30);
		} else {
			return false;
		}
	}
}
