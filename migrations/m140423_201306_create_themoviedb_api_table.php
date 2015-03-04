<?php

use \yii\db\Migration;

class m140423_201306_create_themoviedb_api_table extends Migration
{
	public function up()
	{
		$this->createTable('{{%themoviedb_rate}}', [
			'id' => 'bigint unsigned NOT NULL AUTO_INCREMENT COMMENT "ID"',
			'created_at' => 'timestamp DEFAULT CURRENT_TIMESTAMP COMMENT "Created at"',
			'PRIMARY KEY ([[id]])',
			'KEY created_at ([[created_at]])',
		]);
	}

	public function down()
	{
		$this->dropTable('{{%themoviedb_rate}}');

		return true;
	}
}
