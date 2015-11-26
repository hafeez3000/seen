<?php

use \app\components\rules\ViewEmailsInGroupRule;
use \app\components\rules\ReplyEmailsInGroupRule;
use \app\components\rules\ManageEmailGroupsRule;

use \yii\db\Migration;

class m140427_150000_rbac_init extends Migration
{
	public function up()
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

	public function down()
	{
		Yii::$app->db->createCommand('DELETE FROM {{%auth_assignment}}')->execute();
		Yii::$app->db->createCommand('DELETE FROM {{%auth_item_child}}')->execute();
		Yii::$app->db->createCommand('DELETE FROM {{%auth_item}}')->execute();
		Yii::$app->db->createCommand('DELETE FROM {{%auth_rule}}')->execute();
	}
}
