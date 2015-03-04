<?php

use \yii\db\Migration;

class m140425_100755_update_themoviedb_rate extends Migration
{
	public function up()
	{
		$this->alterColumn('{{%themoviedb_rate}}', 'created_at', 'DATETIME NULL DEFAULT NULL COMMENT "Created at"');
		$this->truncateTable('{{%themoviedb_rate}}');
	}

	public function down()
	{
		$this->alterColumn('{{%themoviedb_rate}}', 'created_at', 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT "Created at"');

		return false;
	}
}
