<?php

use yii\db\Schema;
use yii\db\Migration;

class m151207_085439_sync_status_length extends Migration
{
	public function up()
	{
		$this->alterColumn('{{%sync_status}}', 'value', 'MEDIUMTEXT');
	}

	public function down()
	{
		$this->alterColumn('{{%sync_status}}', 'value', 'TEXT');
	}
}
