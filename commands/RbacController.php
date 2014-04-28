<?php namespace app\commands;

use \Yii;
use \yii\console\Controller;

class RbacController extends Controller
{
	public function actionAssign($userid, $rolename)
	{
		$auth = Yii::$app->authManager;

		$role = $auth->getRole($rolename);
		if ($role === null) {
			echo "No role named {$rolename} found!\n";
			return false;
		}

		$user = \app\models\User::findOne($userid);
		if ($role === null) {
			echo "No user found with ID #{$userid}!\n";
			return false;
		}

		try {
			$auth->assign($role, $user->id);
		} catch (\Exception $e) {
			echo "Role could not be assigned. Perhaps the role is already assigned?\n";
			return false;
		}

		return true;
	}

	public function actionDelete()
	{
		if ($this->confirm('Delete all rules?')) {
			Yii::$app->db->createCommand('DELETE FROM {{%auth_assignment}}')->execute();
			Yii::$app->db->createCommand('DELETE FROM {{%auth_item_child}}')->execute();
			Yii::$app->db->createCommand('DELETE FROM {{%auth_item}}')->execute();
			Yii::$app->db->createCommand('DELETE FROM {{%auth_rule}}')->execute();
		}
	}
}