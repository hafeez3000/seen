<?php

use \yii\db\Migration;

class m140505_093820_rbac_manage_languages extends Migration
{
	public function up()
	{
		$auth = Yii::$app->authManager;
		$admin = $auth->getRole('admin');

		// Manage languages
		$manageLanguages = $auth->createPermission('manageLanguages');
		$manageLanguages->description = 'Manage languages';
		$auth->add($manageLanguages);

		$auth->addChild($admin, $manageLanguages);
	}

	public function down()
	{
		$auth = Yii::$app->authManager;
		$admin = $auth->getRole('admin');

		$manageLanguages = $auth->getPermission('manageLanguages');
		$auth->removeChild($admin, $manageLanguages);

		$auth->remove($manageLanguages);

		return true;
	}
}
