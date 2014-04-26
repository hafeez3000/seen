<?php

use \yii\db\Schema;
use \yii\db\Migration;

class m140426_114710_user_timezone extends Migration
{
	public function up()
	{
		$this->addColumn('{{%user}}', 'timezone', 'VARCHAR(100) DEFAULT "UTC" COMMENT "Timezone" AFTER [[language_id]]');
	}

	public function down()
	{
		$this->dropColumn('{{%user}}', 'timezone');

		return true;
	}
}
