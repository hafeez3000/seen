<?php

use \yii\db\Schema;
use \yii\db\Migration;

class m140520_171124_rbac_users extends Migration
{
	public function up()
	{
		$auth = Yii::$app->authManager;
		$admin = $auth->getRole('admin');

		// View log messages
		$viewUsers = $auth->createPermission('viewUsers');
		$viewUsers->description = 'View user details';
		$auth->add($viewUsers);

		$auth->addChild($admin, $viewUsers);

		$this->dropColumn('{{%user}}', 'level');
	}

	public function down()
	{
		$auth = Yii::$app->authManager;
		$admin = $auth->getRole('admin');

		$viewUsers = $auth->getPermission('viewUsers');
		$auth->removeChild($admin, $viewUsers);

		$auth->remove($viewUsers);

		$this->addColumn('{{%user}}', 'level', 'tinyint UNSIGNED DEFAULT 0 AFTER [[timezone]]');

		return true;
	}
}
