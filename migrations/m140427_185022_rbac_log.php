<?php

use \yii\db\Migration;

class m140427_185022_rbac_log extends Migration
{
	public function up()
	{
		$auth = Yii::$app->authManager;
		$admin = $auth->getRole('admin');

		// View log messages
		$viewLogs = $auth->createPermission('viewLogs');
		$viewLogs->description = 'View log messages';
		$auth->add($viewLogs);

		$auth->addChild($admin, $viewLogs);
	}

	public function down()
	{
		$auth = Yii::$app->authManager;
		$admin = $auth->getRole('admin');

		$viewLogs = $auth->getPermission('viewLogs');
		$auth->removeChild($admin, $viewLogs);

		$auth->remove($viewLogs);
	}
}
