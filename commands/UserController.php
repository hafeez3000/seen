<?php namespace app\commands;

use \Yii;
use \yii\console\Controller;

use \app\models\User;

class UserController extends Controller
{
	public function actionGeneratePassword($password)
	{
		$user = new User;

		echo "Password {$password} encrypted: " . $user->encryptPassword($password) . "\n";
	}
}
