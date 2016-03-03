<?php

use \yii\db\Migration;

class m140516_201817_sync_status extends Migration
{
	public function up()
	{
		$this->createTable('{{%sync_status}}', [
			'name' => 'varchar(255) NOT NULL',
			'updated' => 'date DEFAULT "0000-00-00"',
			'value' => 'TEXT DEFAULT NULL',
			'PRIMARY KEY([[name]], [[updated]])',
		]);
	}

	public function down()
	{
		$this->dropTable('{{%sync_status}}');

		return true;
	}
}
