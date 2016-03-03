<?php

use \yii\db\Migration;

class m140514_134602_rbac_updates extends Migration
{
	public function up()
	{
		$auth = Yii::$app->authManager;
		$admin = $auth->getRole('admin');

		// View log messages
		$viewUpdates = $auth->createPermission('viewUpdates');
		$viewUpdates->description = 'View outstanding sync updates';
		$auth->add($viewUpdates);

		$auth->addChild($admin, $viewUpdates);
	}

	public function down()
	{
		$auth = Yii::$app->authManager;
		$admin = $auth->getRole('admin');

		$viewUpdates = $auth->getPermission('viewUpdates');
		$auth->removeChild($admin, $viewUpdates);

		$auth->remove($viewUpdates);

		return true;
	}
}
