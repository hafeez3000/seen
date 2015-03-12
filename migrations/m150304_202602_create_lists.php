<?php

use yii\db\Schema;
use yii\db\Migration;

class m150304_202602_create_lists extends Migration
{
	public function up()
	{
		$this->createTable('{{%list}}', [
			'id' => 'int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT "Movie ID"',
			'user_id' => 'int(10) UNSIGNED NOT NULL',
			'title' => 'varchar(100) NOT NULL',
			'slug' => 'varchar(100) NOT NULL',
			'description' => 'text DEFAULT NULL',
			'public' => 'boolean DEFAULT 1',
			'highlighted' => 'boolean DEFAULT 0',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'updated_at' => 'datetime DEFAULT NULL COMMENT "Updated at"',
			'deleted_at' => 'datetime DEFAULT NULL COMMENT "Deleted at"',
			'PRIMARY KEY (id)',
			'INDEX user_id ([[user_id]])',
			'INDEX slug ([[slug]])',
		]);
		$this->addForeignKey('list_user_id', '{{%list}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%list_entry}}', [
			'id' => 'int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT "Movie ID"',
			'list_id' => 'int(10) UNSIGNED NOT NULL',
			'type' => 'tinyint(3) UNSIGNED NOT NULL COMMENT "0: Movie, 1: TV Show, 2: Person"',
			'themoviedb_id' => 'int(10) UNSIGNED NOT NULL',
			'description' => 'text DEFAULT NULL',
			'position' => 'smallint(5) UNSIGNED NOT NULL',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'updated_at' => 'datetime DEFAULT NULL COMMENT "Updated at"',
			'deleted_at' => 'datetime DEFAULT NULL COMMENT "Deleted at"',
			'PRIMARY KEY (id)',
			'INDEX list_id ([[list_id]])',
			'INDEX themoviedb_id ([[themoviedb_id]])',
		]);
		$this->addForeignKey('list_entry_list_id', '{{%list_entry}}', 'list_id', '{{%list}}', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
		$this->dropTable('{{%list_entry}}');
		$this->dropTable('{{%list}}');
	}
}
