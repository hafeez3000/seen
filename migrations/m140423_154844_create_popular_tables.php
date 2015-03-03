<?php

class m140423_154844_create_popular_tables extends \yii\db\Migration
{
	public function up()
	{
		$this->createTable('{{%show_popular}}', [
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT "ID"',
			'show_id' => 'int(10) unsigned NOT NULL COMMENT "Show"',
			'order' => 'tinyint(4) unsigned NOT NULL COMMENT "Order"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'PRIMARY KEY ([[id]])',
			'KEY show_id ([[show_id]])',
		]);
		$this->addForeignKey('show_popular_show_id', '{{%show_popular}}', 'show_id', '{{%show}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%movie_popular}}', [
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT "ID"',
			'movie_id' => 'int(10) unsigned NOT NULL COMMENT "Movie"',
			'order' => 'tinyint(4) unsigned NOT NULL COMMENT "Order"',
			'created_at' => 'datetime DEFAULT NULL COMMENT "Created at"',
			'PRIMARY KEY ([[id]])',
			'KEY movie_id ([[movie_id]])',
		]);
		$this->addForeignKey('movie_popular_movie_id', '{{%movie_popular}}', 'movie_id', '{{%movie}}', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
		$this->dropTable('{{%show_popular}}');
		$this->dropTable('{{%movie_popular}}');

		return true;
	}
}
