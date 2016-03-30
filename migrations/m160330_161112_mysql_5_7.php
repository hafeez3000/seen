<?php

use yii\db\Migration;

class m160330_161112_mysql_5_7 extends Migration
{
	public function up()
	{
		$this->alterColumn('{{%user}}', 'name', 'VARCHAR(100) DEFAULT ""');
	}

	public function down()
	{
		$this->alterColumn('{{%user}}', 'name', 'VARCHAR(100) DEFAULT NULL');
	}
}
