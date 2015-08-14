<?php

use yii\db\Schema;
use yii\db\Migration;

class m150814_161028_basic_auth extends Migration
{
	public function up()
	{
		$this->createTable('{{%basic_auth_key}}', [
			'id' => 'int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT "Auth Key ID"',
			'user_id' => 'int(10) UNSIGNED NOT NULL',
			'key' => 'varchar(128) NOT NULL',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'updated_at' => 'datetime DEFAULT NULL COMMENT "Updated at"',
			'deleted_at' => 'datetime DEFAULT NULL COMMENT "Deleted at"',
			'PRIMARY KEY (id)',
			'INDEX `user_id` ([[user_id]])',
			'UNIQUE KEY `key` ([[key]])',
		]);
		$this->addForeignKey('key_user_id', '{{%basic_auth_key}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
		$this->dropTable('{{%basic_auth_key}}');
	}
}
