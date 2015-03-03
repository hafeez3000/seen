<?php

class m140728_170901_add_log_prefix extends \yii\db\Migration
{
	public function up()
	{
		$this->addColumn('{{%log}}', 'prefix', 'TEXT');
	}

	public function down()
	{
		$this->dropColumn('{{%log}}', 'prefix');

		return true;
	}
}
