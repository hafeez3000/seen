<?php namespace app\commands;

use \Yii;
use \yii\console\Controller;

use \app\components\rules\ViewEmailsInGroupRule;
use \app\components\rules\ReplyEmailsInGroupRule;
use \app\components\rules\ManageEmailGroupsRule;

class RbacController extends Controller
{
	public function actionInit()
	{
		$auth = Yii::$app->authManager;

		// View emails in general
		$viewEmails = $auth->createPermission('viewEmails');
		$viewEmails->description = 'View emails in general';
		$auth->add($viewEmails);

		// View all emails
		$viewAllEmails = $auth->createPermission('viewAllEmails');
		$viewAllEmails->description = 'View all emails';
		$auth->add($viewAllEmails);

		// View only emails in a specific group
		$viewEmailsInGroupRule = new ViewEmailsInGroupRule;
		$auth->add($viewEmailsInGroupRule);

		$viewEmailsInGroup = $auth->createPermission('viewEmailsInGroup');
		$viewEmailsInGroup->description = 'View emails in specific group';
		$viewEmailsInGroup->ruleName = $viewEmailsInGroupRule->name;
		$auth->add($viewEmailsInGroup);

		// Reply to all emails
		$replyAllEmails = $auth->createPermission('replyAllEmails');
		$replyAllEmails->description = 'Reply to all emails';
		$auth->add($replyAllEmails);

		// Reply only to emails in a specific group
		$replyEmailsInGroupRule = new ReplyEmailsInGroupRule;
		$auth->add($replyEmailsInGroupRule);

		$replyEmailsInGroup = $auth->createPermission('replyEmailsInGroup');
		$replyEmailsInGroup->description = 'Reply to emails in a specific group';
		$replyEmailsInGroup->ruleName = $replyEmailsInGroupRule->name;
		$auth->add($replyEmailsInGroup);

		// Manage groups
		$manageEmailGroupsRule = new ManageEmailGroupsRule;
		$auth->add($manageEmailGroupsRule);

		$manageEmailGroups = $auth->createPermission('manageEmailGroups');
		$manageEmailGroups->description = 'Manage email groups';
		$manageEmailGroups->ruleName = $manageEmailGroupsRule->name;
		$auth->add($manageEmailGroups);

		// Create supporter role
		$supporter = $auth->createRole('supporter');
		$auth->add($supporter);
		$auth->addChild($supporter, $viewEmails);
		$auth->addChild($supporter, $viewEmailsInGroup);
		$auth->addChild($supporter, $replyEmailsInGroup);

		// Create admin role
		$admin = $auth->createRole('admin');
		$auth->add($admin);
		$auth->addChild($admin, $supporter);
		$auth->addChild($admin, $viewAllEmails);
		$auth->addChild($admin, $replyAllEmails);
		$auth->addChild($admin, $manageEmailGroups);
	}

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