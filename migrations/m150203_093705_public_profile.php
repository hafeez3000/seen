<?php

use yii\db\Schema;
use yii\db\Migration;

class m150203_093705_public_profile extends Migration
{
	public function up()
	{
		$this->addColumn('{{%user}}', 'profile_public', 'boolean NOT NULL DEFAULT false AFTER [[api_key]]');
		$this->addColumn('{{%user}}', 'profile_name', 'varchar(64) DEFAULT NULL AFTER [[profile_public]]');

		$this->renameColumn('{{%user}}', 'api_key', 'auth_key');
	}

	public function down()
	{
		$this->dropColumn('{{%user}}', 'profile_public');
		$this->dropColumn('{{%user}}', 'profile_name');

		$this->renameColumn('{{%user}}', 'auth_key', 'api_key');

		return true;
	}
}
