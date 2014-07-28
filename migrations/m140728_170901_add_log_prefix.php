<?php

use \yii\db\Schema;
use \yii\db\Migration;

class m140728_170901_add_log_prefix extends Migration
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
